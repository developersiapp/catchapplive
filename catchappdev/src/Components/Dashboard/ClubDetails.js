import React, { Component } from "react";
import Header from "../HeadFoot/Header.js";
import Footer from "../HeadFoot/Footer.js";
import { fetchClubDetails } from "../../Actions/dashboardActions.js";
import {
  createStream,
  startStream,
  stopStream,
  checkStreamState,
  setStreamDetails,
} from "../../Actions/streamAction.js";
import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import "../../Assets/rangeslider.min.css";
import "../../Assets/clubDetails.css";
//import Slider from 'react-rangeslider'
//import 'react-rangeslider/lib/index.css'
import InputRange from "react-input-range";
import "react-input-range/lib/css/index.css";
import adapter from "webrtc-adapter";
import LoadingOverlay from "react-loading-overlay";
import ScaleLoader from "react-spinners/ScaleLoader";
import WaveForm from "./WaveForm.js";
import { ReactMic } from "react-mic";
import { toast } from "react-toastify";
import { isFormSubmit } from "../../Utils/services.js";
import ReactPlayer from "react-player";

if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
  //console.log("enumerateDevices() not supported.");
}

var userInfo = { param1: "value1" };
var localStream = null;
var newAPI = false;
var peerConnection = null;
var videoIndex = -1;
var audioIndex = -1;

navigator.getUserMedia =
  navigator.getUserMedia ||
  navigator.mozGetUserMedia ||
  navigator.webkitGetUserMedia;
window.RTCPeerConnection =
  window.RTCPeerConnection ||
  window.mozRTCPeerConnection ||
  window.webkitRTCPeerConnection;
window.RTCIceCandidate =
  window.RTCIceCandidate ||
  window.mozRTCIceCandidate ||
  window.webkitRTCIceCandidate;
window.RTCSessionDescription =
  window.RTCSessionDescription ||
  window.mozRTCSessionDescription ||
  window.webkitRTCSessionDescription;

var wsURL;
var streamInfo = { applicationName: "", streamName: "", sessionId: "[empty]" };
var videoBitrate = 360;
var audioBitrate = 64;
var videoFrameRate = "29.97";
var videoChoice = "42e01f";
var audioChoice = "Opus";
var wsConnection;
var peerConnectionConfig = { iceServers: [] };
var SDPOutput = new Object();
var waveStart = false;
var count = 0;
var streamingStatus = false;
var startMe = 0;
class ClubDetails extends Component {
  constructor(props) {
    super(props);
    const userData = JSON.parse(localStorage.getItem("userData"));
    const isLoggedIn = JSON.parse(localStorage.getItem("isLoggedIn"));
    const userType = localStorage.getItem("user-type");

    this.state = {
      logged_in: userData ? userData.logged_in : false,
      isLoggedIn: isLoggedIn ? isLoggedIn : 0,
      location: userData ? userData.location : "",
      userProfile: userData ? userData.profile_image : "",
      djName: userData ? userData.name : "",
      userName: userData ? userData.first_name + " " + userData.last_name : "",
      userId: userData ? userData.id : "",
      clubId: "",
      clubDetails: {},
      clubData: {},
      startStream: {},
      createData: {},
      profileImage: "",
      streamId: "",
      female: 0,
      male: 0,
      player: "",
      showLoader: false,
      userType: userType ? userType : "",
      streamName: "",
      appName: "",
      streamUrl: "",
      startStream: false,
      totalTraffic: 30,
      clubImage: "",
      waveStart: false,
      playing: false,
      streamStatus: "",
      activeSlow: true,
      activeMed: false,
      activeHype: false,
      internalStatus: false,
      playStreamig: false,
      ipAddress: "",
      stopStream: false,
      error: "",
      alreadyPlaying: false,
      streamPlayerUrl: "",
    };
  }

  getMediaDevice = () => {
    var streamAudio = document.getElementById("Jazz");

    var constraints = {
      video: false,
      audio: true,
    };

    if (navigator.mediaDevices.getUserMedia) {
      navigator.mediaDevices
        .getUserMedia(constraints)
        .then((stream) => {
          localStream = stream;
          try {
            streamAudio.srcObject = stream;
            this.setState({ error: "" });
          } catch (error) {
            streamAudio.src = window.URL.createObjectURL(stream);
          }
        })
        .catch((error) => {
          if (error) {
            toast.error("Mic not found!");
            this.setState({ error: error });
          }
        });

      newAPI = false;
    } else {
      alert("Your browser does not support getUserMedia API");
    }
  };

  getStreamStatus = () => {
    let streamId = this.state.streamId;
    this.props.checkStreamState({
      stream_id: streamId,
      web: true,
      showLoader: true,
    });
  };

  handleInputChange = (event) => {
    const target = event.target;
    const value = target.type === "checkbox" ? target.checked : target.value;
    this.setState({
      [event.target.name]: value,
    });
  };

  componentDidMount() {
    if (
      this.state.userType == "user" &&
      (this.state.logged_in !== true ||
        this.state.isLoggedIn !== 1 ||
        this.state.authKey == "")
    ) {
      window.location.href = `/`;
    }
    window.scrollTo(0, 0);
    let clubId = this.props.match.params.id ? this.props.match.params.id : "";
    let formData = {
      stream_name: "Live Stream",
      transcoder_type: "transcoded",
      billing_mode: "pay_as_you_go",
      broadcast_location: "asia_pacific_india",
      encoder: "other_webrtc",
      aspect_ratio_width: 1920,
      aspect_ratio_height: 1080,
      club_id: clubId,
    };
    this.setState({ showLoader: true });
    if (clubId) {
      this.props.fetchClubDetails({
        club_id: clubId,
        dj_id: this.state.userId,
        web: true,
      });
    }
    if (this.state.userType == "dj") {
      this.props.createStream(formData);
    }
    this.getMediaDevice();
    let streamData = {
      club_id: clubId,
      dj_id: this.state.userId,
      start_time: Math.floor(Date.now() / 1000),
      female_count: this.state.female,
      male_count: this.state.male,
      traffic: "Slow",
      web: true,
    };

    this.props.setStreamDetails(streamData);
  }

  componentWillUnmount() {
    if (streamingStatus == true) {
      this.stopStreaming();
      count = 0;
      streamingStatus = false;
    }
    clearInterval(this.getStreamStatus);
  }

  static getDerivedStateFromProps(props, state) {
    let returnState = {};
    if (
      props.clubDetails !== undefined &&
      props.clubDetails.success == true &&
      props.clubDetails !== state.clubDetails
    ) {
      returnState.clubDetails = props.clubDetails ? props.clubDetails : {};
      returnState.clubData = props.clubDetails.data
        ? props.clubDetails.data
        : {};
      returnState.male = props.clubDetails.data
        ? props.clubDetails.data.male_listeners
        : 0;
      returnState.profileImage = props.clubDetails.data
        ? props.clubDetails.data.profile_image
        : 0;
      returnState.female = props.clubDetails.data
        ? props.clubDetails.data.female_listeners
        : 0;
      /*returnState.totalTraffic = props.clubDetails.data
        ? props.clubDetails.data.total_listeners 
        : 0;*/
      returnState.clubId = props.clubDetails.data
        ? props.clubDetails.data.club_id
        : "";

      returnState.streamPlayerUrl = props.clubDetails.data
        ? props.clubDetails.data.live_stream_url
        : "";
      returnState.iosStreaming = props.clubDetails.data
        ? props.clubDetails.data.ios_streaming
        : 0;

      if (returnState.iosStreaming == true || returnState.iosStreaming == 1) {
        returnState.alreadyPlaying = true;
      }

      returnState.clubImage = props.clubDetails.data
        ? props.clubDetails.data.club_image
        : "";
      // returnState.showLoader = false;
      return returnState;
    }

    if (
      props.createData !== undefined &&
      props.createData !== state.createData &&
      props.createDate !== state.createDate
    ) {
      returnState.createData = props.createData
        ? props.createData.live_stream
        : {};
      returnState.streamId = props.createData.live_stream
        ? props.createData.live_stream.id
        : "";
      returnState.streamUrl = props.createData.live_stream
        ? props.createData.live_stream.source_connection_information.sdp_url
        : "";
      returnState.appName = props.createData.live_stream
        ? props.createData.live_stream.source_connection_information
            .application_name
        : "";

      returnState.streamName = props.createData.live_stream
        ? props.createData.live_stream.source_connection_information.stream_name
        : "";

      returnState.startStream = true;
      returnState.waveStart = false;
      return returnState;
    }
    if (props.startData !== undefined && props.startData !== state.startData) {
      returnState.startStream = true;
      //console.log(props.startData);
      if (props.startData.live_stream) {
        returnState.streamStatus =
          props.startData.live_stream.state == "starting"
            ? props.startData.live_stream.state
            : "started";
        returnState.stopStream = false;
      } else {
        returnState.streamStatus = "started";
        returnState.showLoader = false;
      }
      return returnState;
    }

    if (
      props.streamState !== undefined &&
      props.streamState !== state.streamState
    ) {
      returnState.streamStatus = props.streamState.live_stream
        ? props.streamState.live_stream.state
        : "";
      /* returnState.ipAddress = props.streamState
        ? props.streamState.live_stream.ip_address
        : "";*/

      if (
        props.startStream == true &&
        props.streamState.live_stream.state == "started"
      ) {
        returnState.startStream = false;
      }
      count = 1;
      returnState.showLoader = false;
      if (props.streamState && props.streamState.live_stream) {
        returnState.playStreamig =
          props.streamState.live_stream.state == "stopped" ? true : false;
      }
      if (!returnState.playStreamig && streamingStatus && count == 0) {
        toast.error(
          "Someone is already playing Dj here, please try again later!"
        );
      }

      return returnState;
    }
    return null;
  }

  componentDidUpdate(prevState) {
    if (this.state.startStream && count == 0) {
      this.getStreamStatus();
    }

    if (this.state.startStream && this.state.streamStatus == "starting") {
      setInterval(() => {
        this.getStreamStatus();
      }, 30000);
      if (this.state.streamState == "started") {
        clearInterval();
      }
    }
    if (
      this.state.startStream &&
      this.state.streamStatus == "started" &&
      this.state.playing == true &&
      startMe == 0
    ) {
      //console.log("inside start");
      this.start();
      startMe = 1;
    }
  }

  startStreaming = () => {
    if (isFormSubmit()) {
      if (this.state.error) {
        this.getMediaDevice();
        return;
      }
      if (this.state.female == 0 && this.state.male == 0) {
        toast.error("Please fill the active members");
        return;
      } else {
        let streamId = this.state.streamId;
        startMe = 0;
        this.setState({
          showLoader: true,
          waveStart: true,
          playing: true,
          error: "",
          startStream: true,
        });
        streamingStatus = true;
        count = count + 1;
        this.props.startStream({ stream_id: streamId });
      }
    }
  };

  stopStreaming = () => {
    let streamId = this.state.streamId;
    this.setState({
      waveStart: false,
      playing: false,
      female: 0,
      male: 0,
      totalTraffic: 30,
      startStream: false,
    });

    streamingStatus = false;
    this.props.stopStream({ stream_id: streamId, web: true });
    count = 0;
    startMe = 0;
    peerConnection = null;
    this.trafficSlow();
  };
  checkStreamState = () => {
    let streamId = this.state.streamId;
    this.props.checkStreamState();
  };

  start = () => {
    if (peerConnection == null) this.startPublisher();
    else this.stopPublisher();
  };

  startPublisher = () => {
    wsURL = this.state.streamUrl;
    streamInfo.applicationName = this.state.appName;
    streamInfo.streamName = this.state.streamName;
    videoBitrate = 360;
    audioBitrate = 64;
    videoFrameRate = "24.97";
    videoChoice = "42e01f";
    audioChoice = "opus";

    /*console.log(
      "startPublisher: wsURL:" +
        wsURL +
        " streamInfo:" +
        JSON.stringify(streamInfo)
    );*/

    this.wsConnect(wsURL);
  };

  enhanceSDP = (sdpStr, enhanceData) => {
    var sdpLines = sdpStr.split(/\r\n/);
    var sdpSection = "header";
    var hitMID = false;
    var sdpStrRet = "";

    if (!sdpStr.includes("THIS_IS_SDPARTA") || videoChoice.includes("VP9")) {
      for (var sdpLine of sdpLines) {
        if (sdpLine.length <= 0) continue;

        var doneCheck = this.checkLine(sdpLine);
        if (!doneCheck) continue;

        sdpStrRet += sdpLine;
        sdpStrRet += "\r\n";
      }
      sdpStrRet = this.addAudio(
        sdpStrRet,
        this.deliverCheckLine(audioChoice, "audio")
      );
      sdpStrRet = this.addVideo(
        sdpStrRet,
        this.deliverCheckLine(videoChoice, "video")
      );
      sdpStr = sdpStrRet;
      sdpLines = sdpStr.split(/\r\n/);
      sdpStrRet = "";
    }

    for (var sdpLine of sdpLines) {
      //console.log(sdpLine.length);
      if (sdpLine.length <= 0) continue;

      if (sdpLine.indexOf("m=audio") == 0 && audioIndex != -1) {
        var audioMLines = sdpLine.split(" ");
        sdpStrRet +=
          audioMLines[0] +
          " " +
          audioMLines[1] +
          " " +
          audioMLines[2] +
          " " +
          audioIndex;
      } else if (sdpLine.indexOf("m=video") == 0 && videoIndex != -1) {
        audioMLines = sdpLine.split(" ");
        sdpStrRet +=
          audioMLines[0] +
          " " +
          audioMLines[1] +
          " " +
          audioMLines[2] +
          " " +
          videoIndex;
      } else {
        sdpStrRet += sdpLine;
      }

      if (sdpLine.indexOf("m=audio") === 0) {
        sdpSection = "audio";
        hitMID = false;
      } else if (sdpLine.indexOf("m=video") === 0) {
        sdpSection = "video";
        hitMID = false;
      } else if (sdpLine.indexOf("a=rtpmap") == 0) {
        sdpSection = "bandwidth";
        hitMID = false;
      }

      if (sdpLine.indexOf("a=mid:") === 0 || sdpLine.indexOf("a=rtpmap") == 0) {
        if (!hitMID) {
          if ("audio".localeCompare(sdpSection) == 0) {
            if (enhanceData.audioBitrate !== undefined) {
              sdpStrRet += "\r\nb=CT:" + enhanceData.audioBitrate;
              sdpStrRet += "\r\nb=AS:" + enhanceData.audioBitrate;
            }
            hitMID = true;
          } else if ("video".localeCompare(sdpSection) == 0) {
            if (enhanceData.videoBitrate !== undefined) {
              sdpStrRet += "\r\nb=CT:" + enhanceData.videoBitrate;
              sdpStrRet += "\r\nb=AS:" + enhanceData.videoBitrate;
              if (enhanceData.videoFrameRate !== undefined) {
                sdpStrRet += "\r\na=framerate:" + enhanceData.videoFrameRate;
              }
            }
            hitMID = true;
          } else if ("bandwidth".localeCompare(sdpSection) == 0) {
            var rtpmapID;
            rtpmapID = this.getrtpMapID(sdpLine);
            if (rtpmapID !== null) {
              var match = rtpmapID[2].toLowerCase();
              if (
                "vp9".localeCompare(match) == 0 ||
                "vp8".localeCompare(match) == 0 ||
                "h264".localeCompare(match) == 0 ||
                "red".localeCompare(match) == 0 ||
                "ulpfec".localeCompare(match) == 0 ||
                "rtx".localeCompare(match) == 0
              ) {
                if (enhanceData.videoBitrate !== undefined) {
                  sdpStrRet +=
                    "\r\na=fmtp:" +
                    rtpmapID[1] +
                    " x-google-min-bitrate=" +
                    enhanceData.videoBitrate +
                    ";x-google-max-bitrate=" +
                    enhanceData.videoBitrate;
                }
              }

              if (
                "opus".localeCompare(match) == 0 ||
                "isac".localeCompare(match) == 0 ||
                "g722".localeCompare(match) == 0 ||
                "pcmu".localeCompare(match) == 0 ||
                "pcma".localeCompare(match) == 0 ||
                "cn".localeCompare(match) == 0
              ) {
                if (enhanceData.audioBitrate !== undefined) {
                  sdpStrRet +=
                    "\r\na=fmtp:" +
                    rtpmapID[1] +
                    " x-google-min-bitrate=" +
                    enhanceData.audioBitrate +
                    ";x-google-max-bitrate=" +
                    enhanceData.audioBitrate;
                }
              }
            }
          }
        }
      }
      sdpStrRet += "\r\n";
    }
    // console.log("Resulting SDP: " + sdpStrRet);
    console.log("stream started");
    toast.success("Streaming Started!");
    return sdpStrRet;
  };

  wsConnect = (wsURL) => {
    wsConnection = new WebSocket(wsURL);
    wsConnection.binaryType = "arraybuffer";

    wsConnection.onopen = () => {
      // console.log("wsConnection.onopen");

      peerConnection = new RTCPeerConnection(peerConnectionConfig);
      peerConnection.onicecandidate = this.gotIceCandidate(peerConnection);
      if (newAPI) {
        var localTracks = localStream.getTracks();
        for (var localTrack of localTracks) {
          peerConnection.addTrack(localTrack, localStream);
        }
      } else {
        peerConnection.addStream(localStream);
      }

      peerConnection
        .createOffer()
        .then(this.gotDescription)
        .catch(this.errorHandler);
    };

    wsConnection.onmessage = function (evt) {
      //console.log("wsConnection.onmessage: " + evt.data);

      var msgJSON = JSON.parse(evt.data);

      var msgStatus = Number(msgJSON["status"]);
      var msgCommand = msgJSON["command"];

      if (msgStatus != 200) {
        this.stopPublisher();
      } else {
        var sdpData = msgJSON["sdp"];
        if (sdpData !== undefined) {
          waveStart = true;
          // console.log("sdp: " + msgJSON["sdp"]);

          peerConnection
            .setRemoteDescription(new RTCSessionDescription(sdpData))
            .catch(this.errorHandler);
        }

        var iceCandidates = msgJSON["iceCandidates"];
        if (iceCandidates !== undefined) {
          for (var iceCandidate of iceCandidates) {
            //console.log("iceCandidates: " + iceCandidate);
            waveStart = true;

            peerConnection.addIceCandidate(new RTCIceCandidate(iceCandidate));
          }
        }
      }

      if (wsConnection != null) wsConnection.close();
      wsConnection = null;
    };

    wsConnection.onclose = () => {
      // console.log("wsConnection.onclose");
    };

    wsConnection.onerror = (evt) => {
      console.log("wsConnection.onerror: " + JSON.stringify(evt));
      toast.error("Streaming server error, please try again later!");
      console.log("WebSocket connection failed: " + wsURL);
      this.stopPublisher();
      this.stopStreaming();
      startMe = 0;
      this.setState({ playing: false, startStream: false });
    };
  };

  gotIceCandidate = (event) => {
    //  console.log(event);
    if (event.candidate != null) {
      console.log(
        "gotIceCandidate: " + JSON.stringify({ ice: event.candidate })
      );
    }
  };

  gotDescription = (description) => {
    //console.log(description);
    var enhanceData = new Object();

    if (audioBitrate !== undefined)
      enhanceData.audioBitrate = Number(audioBitrate);
    if (videoBitrate !== undefined)
      enhanceData.videoBitrate = Number(videoBitrate);
    if (videoFrameRate !== undefined)
      enhanceData.videoFrameRate = Number(videoFrameRate);

    description.sdp = this.enhanceSDP(description.sdp, enhanceData);

    //console.log("gotDescription: " + JSON.stringify({ sdp: description }));

    return peerConnection
      .setLocalDescription(description)
      .then(() => {
        wsConnection.send(
          '{"direction":"publish", "command":"sendOffer", "streamInfo":' +
            JSON.stringify(streamInfo) +
            ', "sdp":' +
            JSON.stringify(description) +
            ', "userData":' +
            JSON.stringify(userInfo) +
            "}"
        );
      })
      .catch((err) => {
        console.log("set local description error:");
        console.log(err);
      });
  };

  checkLine = (line) => {
    if (
      line.startsWith("a=rtpmap") ||
      line.startsWith("a=rtcp-fb") ||
      line.startsWith("a=fmtp")
    ) {
      var res = line.split(":");

      if (res.length > 1) {
        var number = res[1].split(" ");
        if (!isNaN(number[0])) {
          if (!number[1].startsWith("http") && !number[1].startsWith("ur")) {
            var currentString = SDPOutput[number[0]];
            if (!currentString) {
              currentString = "";
            }
            currentString += line + "\r\n";
            SDPOutput[number[0]] = currentString;
            return false;
          }
        }
      }
    }

    return true;
  };

  addAudio = (sdpStr, audioLine) => {
    var sdpLines = sdpStr.split(/\r\n/);
    var sdpSection = "header";
    var hitMID = false;
    var sdpStrRet = "";
    var done = false;

    for (var sdpLine of sdpLines) {
      if (sdpLine.length <= 0) continue;

      sdpStrRet += sdpLine;
      sdpStrRet += "\r\n";

      if ("a=rtcp-mux".localeCompare(sdpLine) == 0 && done == false) {
        sdpStrRet += audioLine;
        done = true;
      }
    }
    return sdpStrRet;
  };

  addVideo = (sdpStr, videoLine) => {
    var sdpLines = sdpStr.split(/\r\n/);
    var sdpSection = "header";
    var hitMID = false;
    var sdpStrRet = "";
    var done = false;

    var rtcpSize = false;
    var rtcpMux = false;

    for (var sdpLine of sdpLines) {
      if (sdpLine.length <= 0) continue;

      if (sdpLine.includes("a=rtcp-rsize")) {
        rtcpSize = true;
      }

      if (sdpLine.includes("a=rtcp-mux")) {
        rtcpMux = true;
      }
    }

    for (var sdpLine of sdpLines) {
      sdpStrRet += sdpLine;
      sdpStrRet += "\r\n";

      if (
        "a=rtcp-rsize".localeCompare(sdpLine) == 0 &&
        done == false &&
        rtcpSize == true
      ) {
        sdpStrRet += videoLine;
        done = true;
      }

      if (
        "a=rtcp-mux".localeCompare(sdpLine) == 0 &&
        done == true &&
        rtcpSize == false
      ) {
        sdpStrRet += videoLine;
        done = true;
      }

      if (
        "a=rtcp-mux".localeCompare(sdpLine) == 0 &&
        done == false &&
        rtcpSize == false
      ) {
        done = true;
      }
    }
    return sdpStrRet;
  };

  deliverCheckLine = (profile, type) => {
    var outputString = "";
    for (var line in SDPOutput) {
      var lineInUse = SDPOutput[line];
      outputString += line;
      if (lineInUse.includes(profile)) {
        if (profile.includes("VP9") || profile.includes("VP8")) {
          var output = "";
          var outputs = lineInUse.split(/\r\n/);
          for (var position in outputs) {
            var transport = outputs[position];
            if (
              transport.indexOf("transport-cc") !== -1 ||
              transport.indexOf("goog-remb") !== -1 ||
              transport.indexOf("nack") !== -1
            ) {
              continue;
            }
            output += transport;
            output += "\r\n";
          }

          if (type.includes("audio")) {
            audioIndex = line;
          }

          if (type.includes("video")) {
            videoIndex = line;
          }

          return output;
        }
        if (type.includes("audio")) {
          audioIndex = line;
        }

        if (type.includes("video")) {
          videoIndex = line;
        }
        return lineInUse;
      }
    }
    return outputString;
  };

  stopPublisher = () => {
    if (peerConnection != null) peerConnection.close();
    peerConnection = null;

    if (wsConnection != null) wsConnection.close();
    wsConnection = null;
    // waveStart = false
  };

  getrtpMapID = (line) => {
    console.log(line);
    var findid = new RegExp("a=rtpmap:(\\d+) (\\w+)/(\\d+)");
    var found = line.match(findid);
    return found && found.length >= 3 ? found : null;
  };

  errorHandler = (error) => {
    console.log(error);
  };

  setTrafficData = () => {
    let trafficData;

    if (this.state.totalTraffic < 31) {
      trafficData = "Slow";
    }
    if (this.state.totalTraffic > 31 && this.state.totalTraffic < 51) {
      trafficData = "Normal";
    }
    if (this.state.totalTraffic > 60) {
      trafficData = "Hype";
    }
    let formData = {
      club_id: this.state.clubId,
      dj_id: this.state.userId,
      start_time: Math.floor(Date.now() / 1000),
      female_count: this.state.female,
      male_count: this.state.male,
      traffic: trafficData,
      web: true,
    };

    this.props.setStreamDetails(formData);
  };

  trafficSlow = () => {
    this.setState({
      totalTraffic: 30,
      condition: "Slow",
      activeSlow: true,
      activeMed: false,
      activeHype: false,
    });
    setTimeout(() => {
      this.setTrafficData();
    }, 0);
  };

  trafficMed = () => {
    this.setState({
      condition: "Normal",
      totalTraffic: 50,
      activeSlow: false,
      activeMed: true,
      activeHype: false,
    });
    setTimeout(() => {
      this.setTrafficData();
    }, 0);
  };
  trafficHype = () => {
    this.setState({
      totalTraffic: 100,
      activeSlow: false,
      activeMed: false,
      activeHype: true,
    });
    setTimeout(() => {
      this.setTrafficData();
    }, 0);
  };

  dismissNoModal = () => {
    return <div>{this.props.history.push(`/dashboard`)}</div>;
  };

  onStart = () => {
    this.setState({
      alreadyPlaying: true,
      showLoader: false,
    });
  };

  played = () => {
    this.setState({
      alreadyPlaying: true,
      showLoader: false,
    });
  };

  onDuration = () => {
    this.setState({
      alreadyPlaying: true,
      showLoader: false,
    });
  };

  onError = (error) => {
    if ((error = "hlsError")) {
      this.setState({ showLoader: false });
    }
  };
  render() {
    const { female, male, totalTraffic } = this.state;

    return (
      <div>
        <LoadingOverlay
          active={this.state.showLoader}
          spinner={<ScaleLoader color={"#fb556b"} />}
          text={"Loading"}
        >
          <div className="container-fluid menu">
            <div className="container">
              <Header
                userProfile={this.state.userProfile}
                userName={
                  this.state.djName ? this.state.djName : this.state.userName
                }
              />
            </div>
          </div>
          <div className="container">
            <section className="player">
              <div className="row player-row">
                <div className="col-12 col-xl-12 player-section">
                  <div className="media-box">
                    <h2>{this.state.clubData.club_name}</h2>
                    <div className="media-player">
                      <img
                        src={"/img/media-player1-min.png"}
                        onClick={this.stopStreaming}
                      />
                      {!this.state.playing && (
                        <div
                          className="play button"
                          onClick={this.startStreaming}
                        >
                          <span className={"inner-play"}>{"▶"}</span>
                        </div>
                      )}
                      {this.state.playing && (
                        <div
                          className="play button"
                          onClick={this.stopStreaming}
                        >
                          <span className={"inner-play"}>{"■"}</span>
                        </div>
                      )}
                    </div>
                    <ReactMic
                      record={this.state.waveStart ? true : false}
                      className="sound-wave"
                      onStop={this.onStop}
                      onData={this.onData}
                      strokeColor="#000000"
                      backgroundColor="#fff"
                    />
                  </div>
                </div>
              </div>
              <div>
                <input type="hidden" id="userAgent" name="userAgent" value="" />
                <video
                  id="Jazz"
                  autoPlay
                  playsInline
                  ref={this.audio}
                  muted
                  controls
                  style={{ height: "80px" }}
                  className={"no-display"}
                ></video>
              </div>
              <div className="row player-row">
                <div className="col-12 col-lg-6 col-xl-6">
                  <div className="player-box p1">
                    <div className="row heading-row">
                      <div className="col-12 col-xl-6 player-sub-heading">
                        Active Members
                      </div>
                      <div className="col-12 col-xl-6">
                        {/*<button
                          type="button"
                          onClick={this.startStreaming}
                          className="btn go-out float-right"
                        >
                          <img src="/img/go-out-icon.png" /> GO OUT
                        </button>*/}
                      </div>
                    </div>

                    <div className="row slider-row mt-5 mb-4">
                      <div className="col-xl-12 col-12">
                        <div className="rangeslider">
                          <div className="min-value"></div>
                          <div className="range-slider-design female-slider">
                            <div id="slider_1">
                              <InputRange
                                maxValue={100}
                                minValue={0}
                                value={this.state.female}
                                onChange={(female) => this.setState({ female })}
                                onChangeComplete={(e) => this.setTrafficData(e)}
                              />
                            </div>
                          </div>
                          <div className="gender-text-female">Female</div>
                        </div>
                      </div>
                    </div>

                    <div className="row slider-row mb-manual">
                      <div className="col-xl-12 col-12">
                        <div className="rangeslider">
                          <div className="range-slider-design male-slider">
                            <div id="slider_2">
                              <InputRange
                                maxValue={100}
                                minValue={0}
                                value={this.state.male}
                                onChange={(male) => this.setState({ male })}
                                onChangeComplete={(e) => this.setTrafficData(e)}
                              />
                            </div>
                          </div>
                          <div className="gender-text-male">Male</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="col-12 col-lg-6 col-xl-6">
                  <div className="player-box p2">
                    <div className="row heading-row">
                      <div className="col-12 col-xl-6 player-sub-heading">
                        Traffic
                      </div>
                    </div>
                    <div className="row slider-row mb-manual mt-4">
                      <div className="col-xl-12 col-12">
                        <div className="rangeslider">
                          <div className="range-slider-design male-slider">
                            <div id="slider_3">
                              <InputRange
                                maxValue={100}
                                minValue={0}
                                value={this.state.totalTraffic}
                                disabled
                              />
                            </div>
                          </div>
                          <div className="gender-text-male">Traffic</div>
                        </div>
                      </div>
                      <div className="col-xl-12 col-12 player-btns mb-3">
                        <button
                          type="button"
                          onClick={this.trafficSlow}
                          className={
                            this.state.activeSlow
                              ? "btn btn-default slow active-clr"
                              : "btn btn-default slow"
                          }
                        >
                          Slow
                        </button>
                        <button
                          type="button"
                          onClick={this.trafficMed}
                          className={
                            this.state.activeMed
                              ? "btn btn-default medium active-clr"
                              : "btn btn-default medium"
                          }
                        >
                          Medium
                        </button>
                        <button
                          type="button"
                          onClick={this.trafficHype}
                          className={
                            this.state.activeHype
                              ? "btn btn-default hype active-clr"
                              : "btn btn-default hype"
                          }
                        >
                          Hype
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            <div>
              <span id="sdpDataTag"></span>
            </div>
          </div>
          {this.state.alreadyPlaying && (
            <div
              className="modal fade"
              id="addclub"
              tabIndex="-1"
              role="dialog"
              aria-labelledby="exampleModalLabel"
              aria-hidden="true"
            >
              <div className="modal-dialog" role="document">
                <div className="modal-content noDj-play">
                  <button
                    type="button"
                    className="close text-right"
                    data-dismiss="modal"
                    aria-label="Close"
                    onClick={() => this.dismissNoModal()}
                  >
                    <span aria-hidden="true">
                      <img src="img/close2.png" />
                    </span>
                  </button>
                  <div className="modal-body">
                    <h5>DJ is already playing in this club</h5>
                    <div className="display-block type-something"></div>
                  </div>
                </div>
              </div>
            </div>
          )}

          <Footer />
        </LoadingOverlay>
        <ReactPlayer
          url={this.state.streamPlayerUrl}
          playing={true}
          controls
          onStart={this.onStart}
          onPlay={this.played}
          onEnded={this.playEnd}
          onDuration={this.onDuration}
          className="no-display"
          onError={this.onError}
        />
      </div>
    );
  }
}
function mapStateToProps(state) {
  const returnState = {};
  if (state.DashboardReducer.action === "GET_CLUBDETAILS") {
    if (state.DashboardReducer.data.error == true) {
    } else {
      returnState.clubDetails = state.DashboardReducer.data;
    }
  }
  if (state.StreamReducer.action === "CREATE_STREAM") {
    if (state.StreamReducer.data == undefined) {
    } else {
      returnState.createData = state.StreamReducer.data;
      returnState.createDate = new Date();
    }
  }

  if (state.StreamReducer.action === "START_STREAM") {
    if (state.StreamReducer.data == undefined) {
    } else {
      returnState.startData = state.StreamReducer.data;
      returnState.startDate = new Date();
    }
  }

  if (state.StreamReducer.action === "STREAM_STATE") {
    if (state.StreamReducer.data == undefined) {
    } else {
      returnState.streamState = state.StreamReducer.data;
    }
  }

  if (state.StreamReducer.action === "STOP_STREAM") {
    if (state.StreamReducer.data == undefined) {
    } else {
      returnState.stopData = state.StreamReducer.data;
      returnState.stopDate = new Date();
      //toast.success("Streaming stopped successfully!");
    }
  }

  if (state.StreamReducer.action === "SET_STREAM_DETAILS") {
    if (state.StreamReducer.data == undefined) {
    } else {
      returnState.setStreamDetails = state.StreamReducer.data;
      returnState.setStreamDate = new Date();
    }
  }

  return returnState;
}

function mapDispatchToProps(dispatch) {
  return bindActionCreators(
    {
      fetchClubDetails: fetchClubDetails,
      createStream: createStream,
      startStream: startStream,
      stopStream: stopStream,
      checkStreamState: checkStreamState,
      setStreamDetails: setStreamDetails,
    },
    dispatch
  );
}

export default connect(mapStateToProps, mapDispatchToProps)(ClubDetails);
