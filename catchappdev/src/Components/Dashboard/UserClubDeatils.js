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

class UserClubDetails extends Component {
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
      userName: userData
        ? userData.first_name +
          " " +
          (userData.last_name ? userData.last_name : "")
        : "",
      userId: userData ? userData.user_id : "",
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
      totalTraffic: 0,
      clubImage: "",
      waveStart: false,
      playing: false,
      playStreamig: false,
      traffic: "",
      noDj: false,
      iosStreaming: false,
    };
  }

  handleInputChange = (event) => {
    const target = event.target;
    const value = target.type === "checkbox" ? target.checked : target.value;
    this.setState({
      [event.target.name]: value,
    });
  };

  componentDidMount() {
    if (this.state.userType == "dj") {
      window.location.href = `/dashboard`;
    }
    window.scrollTo(0, 0);
    let clubId = this.props.match.params.id ? this.props.match.params.id : "";
    let userId = this.state.userId;
    let formData = {
      user_id: userId,
      club_id: clubId,
      web: true,
    };
    this.setState({ showLoader: true });
    if (clubId) {
      this.props.fetchClubDetails(formData);
    }
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
      returnState.traffic = props.clubDetails.data
        ? props.clubDetails.data.traffic
        : "";

      if (returnState.female <= 0 && returnState.male <= 0) {
        // toast.error("DJ is not playing here!");
      }
      if (!props.clubDetails.data.female_listeners) {
        if (
          returnState.female == "" ||
          returnState.female == undefined ||
          returnState.female == null
        ) {
          returnState.noDj = true;
          // toast.error("DJ is not playing here!");
        }
      }
      if (returnState.traffic == "Slow") {
        returnState.totalTraffic = 30;
      }
      if (returnState.traffic == "Normal") {
        returnState.totalTraffic = 50;
      }
      if (returnState.traffic == "Hype") {
        returnState.totalTraffic = 100;
      }

      returnState.clubId = props.clubDetails.data
        ? props.clubDetails.data.club_id
        : "";
      returnState.streamUrl = props.clubDetails.data
        ? props.clubDetails.data.live_stream_url
        : "";
      returnState.clubImage = props.clubDetails.data
        ? props.clubDetails.data.club_image
        : "";
      returnState.iosStreaming = props.clubDetails.data.ios_streaming
        ? props.clubDetails.data.ios_streaming
        : 0;

      if (returnState.iosStreaming == true || returnState.iosStreaming == 1) {
        returnState.noDj = false;
      }
      returnState.showLoader = false;
      return returnState;
    }

    return null;
  }

  played = () => {};

  playWave = () => {
    this.setState({
      waveStart: true,
      playing: true,
    });
  };

  stopPlayWave = () => {
    this.setState({
      waveStart: false,
      playing: false,
    });
  };

  dismissModal = () => {
    return <div>{this.props.history.push(`/`)}</div>;
  };

  playEnd = () => {
    this.setState({ noDj: true, playing: false });
  };
  onDuration = (duration) => {};

  onError = (error) => {
    if ((error = "hlsError")) {
      this.setState({ noDj: true, playing: false });
    }
  };

  render() {
    const { female, male, totalTraffic } = this.state;
    //console.log(this.state.noDj);
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
              <div className="row">
                <div className="col-12 col-xl-12 player-section">
                  <div className="media-box">
                    <h2>{this.state.clubData.club_name}</h2>
                    <div className="media-player">
                      <img src={"/img/media-player3-min.png"} />

                      {!this.state.playing && (
                        <div className="play button" onClick={this.playWave}>
                          <span className={"inner-play"}>{"▶"}</span>
                        </div>
                      )}
                      {this.state.playing && (
                        <div
                          className="play button"
                          onClick={this.stopPlayWave}
                        >
                          <span className={"inner-play"}>{"■"}</span>
                        </div>
                      )}
                    </div>
                  </div>
                </div>
              </div>
              <div>
                <ReactPlayer
                  url={this.state.streamUrl}
                  playing={this.state.playing}
                  controls
                  onPlay={this.played}
                  onEnded={this.playEnd}
                  onDuration={this.onDuration}
                  className="no-display"
                  onError={this.onError}
                />
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
                        <div className="rangeslider cursor-none">
                          <div className="min-value"></div>
                          <div className="range-slider-design female-slider">
                            <div id="slider_1">
                              <InputRange
                                maxValue={100}
                                minValue={0}
                                value={this.state.female}
                                onChange={(female) => this.setState({ female })}
                                onChangeComplete={(value) => value}
                                disabled
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
                                onChangeComplete={(value) => value}
                                disabled
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
                                onChange={(totalTraffic) =>
                                  this.setState({ totalTraffic })
                                }
                                disabled
                                onChangeComplete={(value) => value}
                              />
                            </div>
                          </div>
                          <div className="gender-text-male">Traffic</div>
                        </div>
                      </div>
                      <div className="col-xl-12 col-12 player-btns mb-3">
                        <button
                          type="button"
                          className={
                            this.state.totalTraffic > 0 &&
                            this.state.totalTraffic < 31
                              ? "btn btn-default slow active-clr"
                              : "btn btn-default slow"
                          }
                        >
                          Slow
                        </button>
                        <button
                          type="button"
                          className={
                            this.state.totalTraffic > 30 &&
                            this.state.totalTraffic < 61
                              ? "btn btn-default medium active-clr"
                              : "btn btn-default medium"
                          }
                        >
                          Medium
                        </button>
                        <button
                          type="button"
                          className={
                            this.state.totalTraffic > 60 &&
                            this.state.totalTraffic < 101
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
            {this.state.noDj && (
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
                      onClick={() => this.dismissModal()}
                    >
                      <span aria-hidden="true">
                        <img src="img/close2.png" />
                      </span>
                    </button>
                    <div className="modal-body">
                      <h5>No Dj is playing in this club</h5>
                      <div className="display-block type-something"></div>
                    </div>
                  </div>
                </div>
              </div>
            )}
          </div>
          <Footer />
        </LoadingOverlay>
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

export default connect(mapStateToProps, mapDispatchToProps)(UserClubDetails);
