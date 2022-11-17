import React, { Component } from "react";
import Header from "../HeadFoot/Header.js";
import RecentSearch from "./RecentSearch.js";
import Footer from "../HeadFoot/Footer.js";
import {
  fetchClubs,
  searchDj,
  addClubs,
  exportEmptyData,
} from "../../Actions/dashboardActions.js";
import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import "../../Assets/clubDetails.css";
import { ToastContainer, toast } from "react-toastify";
import ScaleLoader from "react-spinners/ScaleLoader";

import LoadingOverlay from "react-loading-overlay";
import { isFormSubmit } from "../../Utils/services.js";

class Dashboard extends Component {
  constructor(props) {
    super(props);
    localStorage.setItem("user-type", "dj");
    const userData = JSON.parse(localStorage.getItem("userData"));
    const isLoggedIn = JSON.parse(localStorage.getItem("isLoggedIn"));
    const userType = localStorage.getItem("user-type");

    this.state = {
      clubs: {},
      clubsList: [],
      suggestionsList: [],
      term: "",
      logged_in: userData ? userData.logged_in : false,
      isLoggedIn: isLoggedIn ? isLoggedIn : 0,
      location: userData ? userData.location : "",
      userProfile: userData
        ? userData.profile_image
        : userData.profile_picture_url,
      userName: userData ? userData.name : "",
      userId: userData ? userData.id : "",
      showLoader: false,
      userType: userType ? userType : "",
      requestedClub: "",
      addClubsError: false,
      addClubs: {},
      showMe: false,
      authKey: userData ? userData.oauth_key : "",
      pictureUrl: "",
      visible: 2,
      userImage: "",
    };
  }

  componentDidMount() {
    window.scrollTo(0, 0);
    if (
      this.state.userType == "user" &&
      (this.state.logged_in !== true ||
        this.state.isLoggedIn !== 1 ||
        this.state.authKey == "")
    ) {
      window.location.href = `/`;
    }

    if (
      this.state.userType == "user" &&
      (this.state.logged_in == true || this.state.isLoggedIn == 1)
    ) {
      window.location.href = `/`;
    }

    let formData = {
      id: this.state.userId,
      city: this.state.location,
      web: true,
    };
    if (
      this.state.userType == "dj" &&
      (this.state.logged_in == true ||
        this.state.isLoggedIn == 1 ||
        this.state.authKey !== "")
    ) {
      this.setState({ showLoader: true });
      this.props.fetchClubs(formData);
    }
  }

  static getDerivedStateFromProps(props, state) {
    props.exportEmptyData();
    let returnState = {};
    if (props.clubs !== undefined && props.clubs !== state.clubs) {
      returnState.clubs = props.clubs ? props.clubs : {};
      returnState.clubsList = props.clubs.clubs ? props.clubs.clubs : [];
      returnState.suggestionsList = props.clubs.suggested_clubs
        ? props.clubs.suggested_clubs
        : [];
      returnState.showLoader = false;
      returnState.userImage = props.clubs
        ? props.clubs.profile_image
        : "https://nwsid.net/wp-content/uploads/2015/05/dummy-profile-pic.png";

      return returnState;
    }
    if (props.addClubsData !== undefined) {
      returnState.showLoader = false;
      returnState.showMe = false;
      return returnState;
    }
    return null;
  }

  handleInputChange = (event) => {
    const target = event.target;
    const value = target.type === "checkbox" ? target.checked : target.value;
    this.setState({
      [event.target.name]: value,
    });
  };

  clubDetails = (clubId) => {
    return <div>{this.props.history.push(`/club-details/${clubId}`)}</div>;
  };

  handleSubmit = (event) => {
    event.preventDefault();
    localStorage.setItem("sortOnly", true);
    let formData = {
      country: this.state.location,
      text: this.state.term,
      id: this.state.userId,
      search: true,
    };
    this.setState({ showLoader: true });
    if (this.state.term) {
      this.props.searchDj(formData);
    } else {
      formData = {
        city: "Atlanta",
        id: this.state.userId,
      };
      this.props.fetchClubs(formData);
    }
  };

  addClubForMe = (e) => {
    if (isFormSubmit) {
      e.preventDefault();
      if (
        this.state.requestedClub == undefined ||
        this.state.requestedClub == null ||
        this.state.requestedClub.trim() == ""
      ) {
        this.setState({ addClubsError: true });
        return;
      }

      let formData = {
        user_id: this.state.userId,
        text: this.state.requestedClub,
      };
      this.setState({ showLoader: true });
      this.props.addClubs(formData);
    }
  };

  showModal = () => {
    this.setState({ requestedClub: "", showMe: true }, () => {});
  };

  dismissModal = () => {
    this.setState({ requestedClub: "", showMe: false });
  };

  loadMore = () => {
    this.setState((prev) => {
      return { visible: prev.visible + 2 };
    });
  };

  render() {
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
                    : this.state.userImage
                }
                userName={this.state.userName}
              />
            </div>
          </div>
          {/*Navigation*/}
          <div className="container">
            {/*Search Bar*/}
            <section className="bg-search">
              <div className="row">
                <div className="col-md-12 col-sm-12 left-login">
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
            {/*Search Bar*/}
            <section className="lounge-recent-search">
              <div className="row">
                <div className="col-12 col-sm-12 col-lg-8 col-xl-8 left-section">
                  {this.state.clubsList &&
                    this.state.clubsList
                      .slice(0, this.state.visible)
                      .map((obj, id) => {
                        return (
                          <div
                            className="display-block lounge-box fade-in"
                            key={id}
                            onClick={this.clubDetails.bind(this, obj.id)}
                          >
                            <div className="row">
                              <div className="col-12 col-lg-12 col-sm-12 like-lounge-box">
                                <span className="name-lounge">{obj.name}</span>
                                {obj.live == false && (
                                  <span className="red-color-list">&nbsp;</span>
                                )}
                                {obj.live && (
                                  <span className="green-color-list">
                                    &nbsp;
                                  </span>
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
                    {this.state.clubsList &&
                      this.state.clubsList.length > this.state.visible && (
                        <button
                          onClick={this.loadMore}
                          type="button"
                          className="load-more btn w-25"
                        >
                          Load more
                        </button>
                      )}
                  </div>
                  {!this.state.clubsList.length && (
                    <div className="display-block lounge-box">
                      <div className="row">
                        <div className="col-12 col-lg-12 col-sm-12 like-lounge-box text-center">
                          {"Sorry! you have no club assigned."}
                        </div>
                        <div className="col-12 col-lg-12 col-sm-12 like-lounge-box text-center">
                          <img src="./img/nodj.png" className={"discImg"} />
                        </div>
                        {/*<div className="col-12 col-lg-12 col-sm-12 add-club-btn text-center">
                          <button
                            className="btn w-50"
                            type="button"
                            onClick={this.showModal}
                          >
                            <img src="./img/more.png" /> Add Club
                          </button>
                        </div>*/}
                      </div>
                    </div>
                  )}
                </div>
                <div className="col-12 col-sm-12 col-lg-4 col-xl-4 left-section">
                  <div className="display-block recent-search-box">
                    <div className="row">
                      <div className="col-12 col-lg-12 col-sm-12 add-club-btn">
                        <button
                          className="btn"
                          type="button"
                          onClick={this.showModal}
                        >
                          <img src="./img/more.png" /> Add Club
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                {/*<RecentSearch
                  suggestionsList={
                    this.state.suggestionsList ? this.state.suggestionsList : []
                  }
                  showModal={() => this.showModal()}
                />*/}
              </div>
              {/*Modal Start*/}
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
                        Send Request to add new Club
                      </h5>
                    </div>
                    <div className="modal-body">
                      <div className="display-block type-something">
                        <textarea
                          className={
                            this.state.addClubsError
                              ? "form-control fieldError"
                              : "form-control"
                          }
                          placeholder="Type club name"
                          value={this.state.requestedClub}
                          onChange={this.handleInputChange}
                          name={"requestedClub"}
                        ></textarea>
                      </div>
                    </div>
                    <div className="modal-footer modal-button">
                      <div className="add-club-btn">
                        <button
                          type="button"
                          className="btn mb-4"
                          onClick={(e) => this.addClubForMe(e)}
                        >
                          Send Request
                        </button>
                      </div>
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
  if (state.DashboardReducer.action === "CLUB_LIST") {
    if (state.DashboardReducer.data.error == true) {
      toast.error("Server error, please try again later!");
    } else {
      returnState.clubs = state.DashboardReducer.data;
    }
  }

  if (state.DashboardReducer.action === "SEARCH_CLUBS") {
    if (state.DashboardReducer.data.error == true) {
    } else {
      returnState.clubs = state.DashboardReducer.data;
    }
  }

  if (state.DashboardReducer.action === "ADD_CLUB") {
    if (state.DashboardReducer.data.error == true) {
      returnState.addClubsData = state.DashboardReducer.data;
      toast.error("Server error, please try again later!");
    } else {
      returnState.addClubsData = state.DashboardReducer.data;
      toast.success("Request sent succesfully!");
    }
  }
  return returnState;
}

function mapDispatchToProps(dispatch) {
  return bindActionCreators(
    {
      fetchClubs: fetchClubs,
      searchDj: searchDj,
      addClubs: addClubs,
      exportEmptyData: exportEmptyData,
    },
    dispatch
  );
}

export default connect(mapStateToProps, mapDispatchToProps)(Dashboard);
