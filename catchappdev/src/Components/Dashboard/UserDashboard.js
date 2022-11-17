import React, { Component } from "react";
import Header from "../HeadFoot/Header.js";
import RecentSearch from "./RecentSearch.js";
import Footer from "../HeadFoot/Footer.js";
import {
  fetchUserClubs,
  exportEmptyData,
  liveDJList,
  searchDj,
  topCities,
  citySearch,
} from "../../Actions/dashboardActions.js";
//import AsyncSelect from "react-select/async";
import Select from "react-select";
import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import "../../Assets/clubDetails.css";
import { ToastContainer, toast } from "react-toastify";
import LoadingOverlay from "react-loading-overlay";
import ScaleLoader from "react-spinners/ScaleLoader";
import UserStories from "./UserStories.js";
import SuggestedClubs from "./SuggestedClubs.js";
import Geocode from "react-geocode";

class UserDashboard extends Component {
  constructor(props) {
    super(props);
    const userData = JSON.parse(localStorage.getItem("userData"));
    const isLoggedIn = JSON.parse(localStorage.getItem("isLoggedIn"));
    const userType = localStorage.getItem("user-type");
    const location = localStorage.getItem("clubLocation");
    localStorage.setItem("user-type", "user");
    this.state = {
      clubs: {},
      nearByList: [],
      suggestionsList: [],
      clubsList: [],
      term: "",
      logged_in: userData ? userData.logged_in : false,
      isLoggedIn: isLoggedIn ? isLoggedIn : 0,
      location: location ? location : "",
      userProfile: userData
        ? userData.profile_image.trim()
        : "https://nwsid.net/wp-content/uploads/2015/05/dummy-profile-pic.png",
      userProfilePicture: userData
        ? userData.profile_picture_url
        : "https://nwsid.net/wp-content/uploads/2015/05/dummy-profile-pic.png",
      djName: userData ? userData.name : "",
      userFirstName: userData ? userData.first_name : "",
      userLastName: userData ? userData.last_name : "",
      userName: "",
      userId: userData ? userData.user_id : 1,
      userType: userType ? userType : "",
      showLoader: false,
      clubId: "",
      currentLocation: location ? location : "",
      authKey: userData ? userData.oauth_key : "",
      city: "",
      noClubs: {},
      visible: 2,
      totalRecords: "",
      limit: 10,
      offset: 0,
      showButton: false,
      searchDJ: false,
      liveClubs: [],
      famousCities: [],
      options: [],
      selectedCity: location ? location : "",
      city_id: "",
      showMatch: false,
    };
    window.scrollTo(0, 0);
  }

  componentDidMount() {
    window.scrollTo(0, 0);
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition((position) => {
        let data = position.coords;

        let lang = data.longitude;
        let lati = data.latitude;
        let placeData = {
          lang: lang,
          lati: lati,
        };

        Geocode.setApiKey("AIzaSyAOosj6Hg1fpweT-KC4FmbzOeZyuhzwdvw");
        //set response language. Defaults to english.
        Geocode.setLanguage("en");
        // set response region. Its optional.
        // A Geocoding request with region=es (Spain) will return the Spanish city.
        Geocode.setRegion("es");

        // Enable or disable logs. Its optional.
        Geocode.enableDebug();

        // Get address from latidude & longitude.
        Geocode.fromLatLng(lati, lang).then(
          (response) => {
            const address = response.results[3].formatted_address;
            //console.log(address);
            const data = address.split(",");
            //  / console.log(data);
            const city = data[data.length - 3];

            if (city == "" || city == undefined || city == null) {
              Geocode.fromLatLng(lati, lang).then((response) => {
                const address = response.results[2].formatted_address;
                //console.log(address);
                const data = address.split(",");
                //  / console.log(data);
                let city = data[data.length - 3];
                localStorage.setItem("clubLocation", city);
                localStorage.setItem("location", city);
                this.setState({ selectedCity: city, currentLocation: city });
              });
            } else {
              localStorage.setItem("clubLocation", city);
              localStorage.setItem("location", city);
              this.setState({ selectedCity: city, currentLocation: city });
            }
          },
          (error) => {
            console.error(error);
          }
        );

        localStorage.setItem("placeData", JSON.stringify(placeData));
      });
    } else {
      localStorage.setItem("clubLocation", "");
      console.error("Geolocation is not supported by this browser!");
    }

    const locationData = localStorage.getItem("clubLocation");
    if (
      this.state.selectedCity !== undefined ||
      this.state.selectedCity !== null ||
      this.state.selectedCity.trim() == ""
    ) {
      this.setState({ showMatch: true });
    }
    this.props.liveDJList();

    const city = "";

    /*if (this.state.userType == "dj") {
      if (
        this.state.logged_in !== true ||
        this.state.isLoggedIn !== 1 ||
        this.state.authKey == ""
      ) {
        window.location.href = `/`;
      }
    }*/
    if (this.state.userType == "dj") {
      if (
        this.state.logged_in == true ||
        this.state.isLoggedIn == 1 ||
        this.state.authKey !== ""
      ) {
        window.location.href = `/dashboard`;
      }
    }

    let formData = {
      // city_name: this.state.location ? this.state.location : locationData,
      city_name: this.state.selectedCity
        ? this.state.selectedCity
        : "Select Location",
      limit: 3,
      offset: 0,
      searchDJ: false,
      user_id: this.state.userId,
    };

    this.setState({
      showLoader: true,
      currentLocation: this.state.location ? this.state.location : locationData,
      location: this.state.location ? this.state.location : locationData,
    });

    this.props.fetchUserClubs(formData);
    this.props.topCities();
    /*if (
      (this.state.userType == "user" && this.state.logged_in == true) ||
      this.state.isLoggedIn == 1 ||
      this.state.authKey !== ""
    ) {
      this.setState({ showLoader: true });
      this.props.fetchUserClubs(formData);
    }*/
  }

  static getDerivedStateFromProps(props, state) {
    let returnState = {};
    props.exportEmptyData();

    if (props.clubs !== undefined && props.clubs !== state.clubs) {
      returnState.clubs = props.clubs ? props.clubs : {};

      returnState.clubsList = [...state.clubsList, ...props.clubs.data];
      //returnState.clubsList = props.clubs.data ? props.clubs.data : [];
      returnState.offset = state.offset + 1;
      let suggestionsList = [];
      returnState.showButton = false;
      if (props.clubs && props.clubs.suggested_clubs) {
        let data = props.clubs.suggested_clubs;
        data.map((obj, id) => {
          if (id < 6) {
            suggestionsList.push(obj);
          }
        });
        returnState.visible = 2;
      }
      returnState.suggestionsList = suggestionsList ? suggestionsList : [];
      returnState.nearByList = props.clubs.nearby_clubs
        ? props.clubs.nearby_clubs
        : [];

      returnState.showMe = false;
      returnState.totalRecords = props.clubs.paginator
        ? props.clubs.paginator.totalRecords
        : 0;
      returnState.limit = props.clubs.paginator
        ? props.clubs.paginator.limit
        : 3;
      returnState.offset = props.clubs.paginator
        ? props.clubs.paginator.offset
        : 0;
      returnState.showButton = false;
      returnState.showLoader = false;
      //returnState.location = props.clubs.location;
      //returnState.currentLocation = props.clubs.location;
    }
    if (
      props.noClubs !== undefined &&
      props.noClubs.error == true &&
      props.noClubs !== state.noClubs
    ) {
      returnState.showMe = false;
      returnState.clubsList = [];
      let suggestionsList = [];
      if (props.noClubs && props.noClubs.suggested_clubs) {
        let data = props.noClubs.suggested_clubs;
        data.map((obj, id) => {
          if (id < 6) {
            suggestionsList.push(obj);
          }
        });
      }
      returnState.showButton = false;
      returnState.suggestionsList = suggestionsList ? suggestionsList : [];
      returnState.nearByList = props.noClubs.nearby_clubs
        ? props.noClubs.nearby_clubs
        : [];
      returnState.showLoader = false;
    }

    if (props.liveClubs !== undefined && props.liveClubs !== state.liveClubs) {
      let liveList = [];
      if (props.liveClubs && props.liveClubs.data.length > 0) {
        let data = props.liveClubs.data;
        data.map((obj, id) => {
          if (id < 6) {
            liveList.push(obj);
          }
        });
      }
      returnState.liveClubs = liveList ? liveList : [];
    }
    if (
      props.famousCities !== undefined &&
      props.famousCities.error == false &&
      props.famousCities !== state.famousCities
    ) {
      returnState.famousCities = props.famousCities
        ? props.famousCities.data
        : [];
    }
    if (
      props.searchCity !== undefined &&
      props.searchCity !== state.searchCity &&
      props.searchCity.error == false
    ) {
      returnState.options = props.searchCity ? props.searchCity.data : [];
    }
    return returnState;
  }

  handleInputChange = (event) => {
    const target = event.target;
    const value = target.type === "checkbox" ? target.checked : target.value;
    //console.log(target.name);
    this.setState({
      [event.target.name]: value,
    });
  };

  clubDetails = (clubId) => {
    if (clubId == undefined || clubId == "") {
      return;
    }

    return <div>{this.props.history.push(`/user-club/${clubId}`)}</div>;
  };

  showLocationModal = () => {
    this.setState({
      showMe: true,
      showMatch: true,
      city: "",
      cityError: "",
    });
  };
  dismissModal = () => {
    this.setState({ showMe: false, showMatch: true, city: "", cityError: "" });
  };

  aClubForMe = () => {
    if (
      this.state.city == undefined ||
      this.state.city == null ||
      this.state.city == "" ||
      this.state.city.trim() == ""
    ) {
      this.setState({ cityError: "fieldError", showMatch: true });
      return;
    }

    let formData = {
      city_name: this.state.city,
      user_id: this.state.userId,
      limit: 3,
      offset: 0,
    };
    localStorage.setItem("clubLocation", this.state.city);
    this.setState({
      showLoader: true,
      currentLocation: this.state.city,
      selectedCity: this.state.city,
      clubsList: [],
      searchDJ: false,
      showMatch: true,
    });
    this.props.fetchUserClubs(formData);
  };

  loadMore = () => {
    const location = localStorage.getItem("clubLocation");
    let formData;
    if (this.state.clubsList.length !== this.state.totalRecords) {
      formData = {
        /*city_name: this.state.currentLocation
          ? this.state.currentLocation
          : "Select Location",*/
        city_name: this.state.selectedCity
          ? this.state.selectedCity
          : location
          ? location
          : "india",
        user_id: this.state.userId,
        offset: this.state.offset + 1,
        limit: this.state.limit,
      };
      this.setState({ showButton: true });
      this.props.fetchUserClubs(formData);
    }
  };

  handleSubmit = (event) => {
    event.preventDefault();
    localStorage.setItem("sortOnly", true);
    let formData = {
      city_name: this.state.selectedCity ? this.state.selectedCity : "India",
      text: this.state.term,
      // user_id: this.state.userId,
      search: true,
      // country: this.state.currentLocation,
    };
    this.setState({ showLoader: true, searchDJ: true, clubsList: [] });
    if (this.state.term) {
      this.props.searchDj(formData);
    } else {
      formData = {
        city_name: this.state.selectedCity ? this.state.selectedCity : "India",
        user_id: this.state.userId,
        limit: 3,
        offset: 0,
      };
      this.setState({ searchDJ: false, clubsList: [] });
      this.props.fetchUserClubs(formData);
    }
  };

  handleSelectChange = (mode, event) => {
    this.props.exportEmptyData();
    const target = event.target;
    let value = target.value;
    let name = event.target.name;

    this.setState({ selectedCity: value });
    if (typeof value === "string") {
      if (value.length >= 2) {
        value = value.trim();
        this.setState({ showMatch: false, options: [] });
        this.props.citySearch({ text: value });
      }
    }
  };

  handleOnFocus = (mode, index, event) => {
    this.props.exportEmptyData();
    //console.log(event.target.value);
    this.setState({ [mode]: true });
    if (typeof value === "string") {
      if (this.state.selectedCity.length < 1) {
        //this.props.citySearch({ text: this.state.selectedCity });
        this.setState({ selectedCity: "", options: [], showMatch: true });
      }
    }
  };

  selectCity = (event) => {
    let formData = {};
    const target = event.target;
    let value = target.value;
    let name = event.target.name;
    let mode = event.currentTarget.dataset.mode;
    let id = event.currentTarget.dataset.id;
    let pname = event.currentTarget.dataset.pname;
    formData = {
      city_name: pname,
      user_id: this.state.userId,
      location: this.state.selectedCity,
      limit: 3,
      offset: 0,
    };
    this.setState({
      city_id: id,
      city_name: pname,
      selectedCity: pname,
      currentLocation: pname,
      options: [],
      showMatch: true,
      showLoader: true,
      clubsList: [],
    });
    localStorage.setItem("clubLocation", pname);
    this.props.fetchUserClubs(formData);
  };

  topCities = (city) => {
    this.setState({
      currentLocation: city ? city : "Select Location",
      selectedCity: city ? city : "Select Location",
      showMatch: true,
      showLoader: true,
      clubsList: [],
      options: [],
    });

    let formData = {
      city_name: city ? city : "Select Location",
      searchDJ: false,
      //user_id: this.state.userId,
      //location: city ? city : "Select Location",
      limit: 3,
      offset: 0,
    };

    localStorage.setItem("clubLocation", city);
    this.props.fetchUserClubs(formData);
  };

  render() {
    let lastName;
    if (
      this.state.userLastName == null ||
      this.state.userLastName == undefined ||
      this.state.userLastName.trim() == ""
    ) {
      lastName = "";
    } else {
      lastName = this.state.userLastName;
    }
    const userName = this.state.userFirstName + " " + lastName;
    // /console.log(this.state.liveClubs);
    var options = [];

    if (this.state.options != undefined && this.state.options.length > 0) {
      options = this.state.options.map((obj, idx) => {
        return { value: obj.id, label: obj.name };
      });
    }
    //console.log(this.state.showMatch);
    return (
      <div>
        <LoadingOverlay
          active={this.state.showLoader}
          spinner={<ScaleLoader color={"#fb556b"} />}
          text={"Loading"}
        >
          {/*Navigation*/}
          <div className="container-fluid menu">
            <div className="container">
              <Header
                userProfile={
                  this.state.userProfile
                    ? this.state.userProfile
                    : this.state.userProfilePicture
                }
                userName={this.state.djName ? this.state.djName : userName}
                showLocationModal={() => {
                  this.showLocationModal();
                }}
              />
            </div>
          </div>
          {/*Navigation*/}
          <div className="container">
            {/*Search Bar*/}
            <UserStories userId={this.state.userId} />
            {/*Search Bar*/}
            <section className="lounge-recent-search">
              <div className="row city-heading">
                <div className="col-12 col-xl-8">
                  <div className="location-dark-container">
                    <div className="row">
                      <div className="col-12 col-xl-6 col-md-6 currentLocation club-search city-search">
                        <div
                          className="location-bg has-search"
                          onClick={this.showLocationModal}
                        >
                          <span className="dashboard-location">
                            <i
                              className="fa fa-map-marker"
                              aria-hidden="true"
                            ></i>
                          </span>
                          <h3>
                            {this.state.currentLocation
                              ? this.state.currentLocation
                              : "Select Location"}
                          </h3>

                          {/*<span className="fa fa-search form-control-feedback"></span>*/}
                          {/*<input
                            className={"form-control"}
                            //name="selectedCity"
                            value={this.state.selectedCity}
                            autoComplete="off"
                            onChange={this.handleSelectChange.bind(
                              this,
                              "selectedCity"
                            )}
                            //onBlur={this.handleOnBlur.bind(this, 'bogo_product_focus')}
                            onFocus={this.handleOnFocus.bind(
                              this,
                              "selectedCity_focus"
                            )}
                            placeholder="Search City"
                            type="text"
                          />
                          {this.state.selectedCity &&
                            this.state.selectedCity !== "" &&
                            this.state.selectedCity.length > 1 &&
                            !this.state.showMatch && (
                              <ul className={"search-dropdown"}>
                                {options.length > 0 ? (
                                  options.map((obj, idx) => {
                                    return (
                                      <li
                                        key={"selectedCity-" + idx}
                                        data-id={obj.value}
                                        data-mode={"selectedCity"}
                                        data-pname={obj.label}
                                        onClick={this.selectCity}
                                      >
                                        <a>{obj && obj.label}</a>
                                      </li>
                                    );
                                  })
                                ) : (
                                  <li
                                    className={
                                      options.length == 0 &&
                                      this.state.selectedCity.length > 1
                                        ? ""
                                        : "no-display"
                                    }
                                    key={"selectedCity-norecord"}
                                    data-id={0}
                                    data-mode={"selectedCity"}
                                    data-pname={"city_match_not_found"}
                                    data-index={-1}
                                  >
                                    <a>{"No match found!"}</a>
                                  </li>
                                )}
                              </ul>
                            )}*/}
                        </div>
                      </div>
                      <div className="col-12 col-md-6 col-xl-6">
                        <section className="club-search">
                          <div className="row">
                            <div className="col-md-12 col-xl-12 col-sm-12 left-login">
                              <form onSubmit={this.handleSubmit}>
                                <div className="form-group has-search">
                                  <span className="fa fa-search form-control-feedback"></span>
                                  <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Search Club"
                                    value={this.state.term}
                                    autoComplete="off"
                                    onChange={this.handleInputChange}
                                    name="term"
                                  />
                                </div>
                              </form>
                            </div>
                          </div>
                        </section>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <h5 className="nearby">Clubs nearby you</h5>

              <div className="row">
                <div className="col-12 col-sm-12 col-lg-8 col-xl-8 left-section">
                  {this.state.clubsList &&
                    this.state.clubsList.map((obj, id) => {
                      return (
                        <div
                          className="display-block lounge-box"
                          key={id}
                          onClick={
                            this.state.userType == "user"
                              ? this.clubDetails.bind(this, obj.club_id)
                              : this.clubDetails.bind(this, obj.id)
                          }
                        >
                          <div className="row fade-in">
                            <div className="col-12 col-lg-12 col-sm-12 like-lounge-box">
                              <span className="name-lounge">{obj.name}</span>
                              {obj.live == false && (
                                <span className="red-color-list">&nbsp;</span>
                              )}
                              {obj.live && (
                                <span className="green-color-list">&nbsp;</span>
                              )}
                            </div>

                            <div className="col-12 col-lg-12 col-sm-12">
                              <div className={"club-image"}>
                                <img src={obj.profile_image} />
                              </div>
                            </div>
                          </div>
                        </div>
                      );
                    })}
                  <div className="col-12 col-lg-12 col-sm-12 text-center add-club-btn load-more">
                    {!this.state.searchDJ &&
                      this.state.clubsList.length > 0 &&
                      this.state.clubsList.length !==
                        this.state.totalRecords && (
                        <button
                          onClick={this.loadMore}
                          type="button"
                          className={
                            this.state.showButton
                              ? "load-more btn w-25 buttonCursor"
                              : "load-more btn w-25"
                          }
                          disabled={this.state.showButton ? true : false}
                        >
                          {this.state.showButton ? "Loading..." : "Load more"}
                        </button>
                      )}
                  </div>
                  {this.state.clubsList && this.state.clubsList.length <= 0 && (
                    <div className="display-block lounge-box pb-4">
                      <div className="row">
                        <div className="col-12 col-lg-12 col-sm-12 like-lounge-box text-center ">
                          {"Sorry! No Club Found."}
                        </div>
                        <div className="col-12 col-lg-12 col-sm-12 like-lounge-box text-center">
                          <img src="./img/nodj.png" className={"discImg"} />
                        </div>
                      </div>
                    </div>
                  )}
                </div>
                <SuggestedClubs
                  suggestionsList={this.state.suggestionsList}
                  nearByList={this.state.nearByList}
                  liveClubs={this.state.liveClubs}
                />
              </div>
            </section>
            {this.state.showMe && (
              <div
                className="modal fade"
                id="addclub"
                tabIndex="-1"
                role="dialog"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
              >
                <div className="modal-dialog" role="document">
                  <div className="modal-content">
                    <button
                      type="button"
                      className="close text-right"
                      data-dismiss="modal"
                      aria-label="Close"
                      onClick={() => this.dismissModal()}
                    >
                      <span aria-hidden="true">
                        <img src="img/close.png" />
                      </span>
                    </button>
                    <div className="modal-header text-center">
                      <h5
                        className="modal-title"
                        id="exampleModalLabel"
                        className="new-club"
                      >
                        Choose Your City
                      </h5>
                    </div>
                    <div className="modal-body">
                      <div className="display-block type-something city-border">
                        <span className="fa fa-search form-control-feedback"></span>
                        <input
                          className={"form-control"}
                          //name="selectedCity"
                          value={this.state.selectedCity}
                          autoComplete="off"
                          onChange={this.handleSelectChange.bind(
                            this,
                            "selectedCity"
                          )}
                          //onBlur={this.handleOnBlur.bind(this, 'bogo_product_focus')}
                          onFocus={this.handleOnFocus.bind(
                            this,
                            "selectedCity_focus"
                          )}
                          placeholder="Search City"
                          type="text"
                        />
                        {this.state.selectedCity &&
                          this.state.selectedCity !== "" &&
                          this.state.selectedCity.length > 1 &&
                          !this.state.showMatch && (
                            <ul className={"search-dropdown"}>
                              {options.length > 0 ? (
                                options.map((obj, idx) => {
                                  return (
                                    <li
                                      key={"selectedCity-" + idx}
                                      data-id={obj.value}
                                      data-mode={"selectedCity"}
                                      data-pname={obj.label}
                                      onClick={this.selectCity}
                                    >
                                      <a>{obj && obj.label}</a>
                                    </li>
                                  );
                                })
                              ) : (
                                <li
                                  className={
                                    options.length == 0 &&
                                    this.state.selectedCity.length > 1
                                      ? "no-match"
                                      : "no-display"
                                  }
                                  key={"selectedCity-norecord"}
                                  data-id={0}
                                  data-mode={"selectedCity"}
                                  data-pname={"city_match_not_found"}
                                  data-index={-1}
                                >
                                  <a>{"No match found!"}</a>
                                </li>
                              )}
                            </ul>
                          )}
                        {/*<select
                          value={this.state.city}
                          onChange={this.handleInputChange}
                          name={"city"}
                          className={
                            this.state.cityError
                              ? "form-control fieldError"
                              : "form-control"
                          }
                        >
                          <option value="">Select City</option>
                          {this.state.famousCities &&
                            this.state.famousCities.length > 0 &&
                            this.state.famousCities.map((obj, id) => {
                              return (
                                <option value={obj.name}>{obj.name}</option>
                              );
                            })}
                          {/*<option value="Anchorage">Anchorage</option>
                          <option value="Atlanta">Atlanta</option>
                          <option value="Bedford heights">
                            Bedford heights
                          </option>
                          <option value="Birmingham">Birmingham</option>
                          <option value="California">California</option>
                          <option value="Cleveland">Cleveland</option>
                          <option value="Columbus">Columbus</option>
                          <option value="Columbus heights">
                            Columbus heights
                          </option>
                          <option value="Dallas">Dallas</option>
                          <option value="Florida">Florida</option>
                          <option value="Georgia">Georgia</option>
                          <option value="Huntsville">Huntsville</option>
                          <option value="Juneau">Juneau</option>
                          <option value="Kampala">Kampala</option>
                          <option value="Lagos">Lagos</option>
                          <option value="Las Vegas">Las Vegas</option>
                          <option value="London">London</option>
                          <option value="Los Angeles">Los Angeles</option>
                          <option value="Manchester">Manchester</option>
                          <option value="New York">New York</option>
                          <option value="Paris">Paris</option>
                          <option value="Tucson">Tucson</option>
                          <option value="Uganda">Uganda</option>}
                        </select>*/}
                      </div>
                      <p className={"modal-line"}></p>
                      <div className="d-block another-city">
                        <h6>Choose Other City</h6>
                        <ul>
                          {this.state.famousCities &&
                            this.state.famousCities.length > 0 &&
                            this.state.famousCities.map((obj, id) => {
                              return (
                                <li
                                  key={id}
                                  onClick={() => this.topCities(obj.name)}
                                >
                                  {obj.name}
                                </li>
                              );
                            })}
                        </ul>

                        <div></div>
                      </div>
                    </div>
                    <div className="modal-footer modal-button">
                      {/*<div className="add-club-btn">
                        <button
                          type="button"
                          className="btn mb-4"
                          onClick={(e) => this.aClubForMe(e)}
                        >
                          Submit
                        </button>
                      </div>*/}
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
  if (state.DashboardReducer.action === "USER_CLUB_LIST") {
    if (state.DashboardReducer.data.error == true) {
      returnState.noClubs = state.DashboardReducer.data;
    } else {
      returnState.clubs = state.DashboardReducer.data;
    }
  }
  if (state.DashboardReducer.action === "SEARCH_CLUBS") {
    if (state.DashboardReducer.data.error == true) {
      //returnState.noClubs = state.DashboardReducer.data;
    } else {
      returnState.clubs = state.DashboardReducer.data;
    }
  }
  if (state.DashboardReducer.action === "LIVE_DJ") {
    if (state.DashboardReducer.data.error == true) {
      //returnState.noClubs = state.DashboardReducer.data;
    } else {
      returnState.liveClubs = state.DashboardReducer.data;
    }
  }
  if (state.DashboardReducer.action === "TOP_CITIES") {
    if (state.DashboardReducer.data.error == true) {
      //returnState.noClubs = state.DashboardReducer.data;
    } else {
      returnState.famousCities = state.DashboardReducer.data;
    }
  }
  if (state.DashboardReducer.action === "SEARCH_CITY") {
    if (state.DashboardReducer.data.error == true) {
      //returnState.noClubs = state.DashboardReducer.data;
    } else {
      returnState.searchCity = state.DashboardReducer.data;
    }
  }
  return returnState;
}

function mapDispatchToProps(dispatch) {
  return bindActionCreators(
    {
      fetchUserClubs: fetchUserClubs,
      exportEmptyData: exportEmptyData,
      searchDj: searchDj,
      liveDJList: liveDJList,
      topCities: topCities,
      citySearch: citySearch,
    },
    dispatch
  );
}

export default connect(mapStateToProps, mapDispatchToProps)(UserDashboard);
