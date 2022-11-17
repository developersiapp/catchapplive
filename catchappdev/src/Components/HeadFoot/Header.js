import React, { Component } from "react";
import { withRouter } from "react-router";
import { logout } from "../../Utils/services.js";

class Header extends Component {
  constructor(props) {
    super(props);
    const userType = localStorage.getItem("user-type");
    const isLoggedIn = JSON.parse(localStorage.getItem("isLoggedIn"));
    this.state = {
      userType: userType ? userType : "",
      showMe: false,
      isLoggedIn: isLoggedIn ? isLoggedIn : 0,
    };
  }
  handleRedirect = () => {
    if (this.state.userType == "dj") {
      return <div>{this.props.history.push(`/dashboard`)}</div>;
    }
    if (this.state.userType == "user") {
      return <div>{this.props.history.push(`/`)}</div>;
    }
  };

  redirectToProfile = () => {
    return <div>{this.props.history.push(`/profile`)}</div>;
  };

  redirectToLogin = () => {
    return <div>{this.props.history.push(`/user-login`)}</div>;
  };

  logoutMe = () => {
    logout();
    setTimeout(() => {
      this.props.history.push("/");
    }, 0);
  };

  redirectToDj = () => {
    return <div>{this.props.history.push(`/dj-login`)}</div>;
  };

  redirectToAbout = () => {
    return <div>{this.props.history.push(`/about-us`)}</div>;
  };

  render() {
    const userType = localStorage.getItem("user-type");
    const { userProfile, userName } = this.props;
    return (
      <div>
        <nav className="navbar navbar-expand-lg navbar-light">
          <button
            className="navbar-toggler"
            type="button"
            data-toggle="collapse"
            data-target="#navbarTogglerDemo02"
            aria-controls="navbarTogglerDemo02"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span className="navbar-toggler-icon"></span>
          </button>
          <a className="navbar-brand" onClick={this.handleRedirect}>
            <img
              className="logo"
              src={userType == "dj" ? "/img/logo.png" : "/img/logo-2.png"}
            />
            <span className="logo-txt">
              {userType == "dj" ? "Catch DJ" : "CatchApp"}
            </span>
            <span className="nightlife">Live Nightlife Experience</span>
            <span className="nightlife desktop-text">
              Live Nightlife Experience
            </span>
          </a>

          <div className="collapse navbar-collapse" id="navbarTogglerDemo02">
            <ul className="navbar-nav mr-auto mt-2 mt-lg-0">
              <li className="nav-item active"></li>
            </ul>
            <form className="form-inline my-2 my-lg-0">
              <ul className="navbar-nav mr-auto mt-2 mt-lg-0">
                {this.state.isLoggedIn == 0 && (
                  <li className="nav-item ">
                    <a
                      className="nav-link about-us"
                      onClick={this.redirectToAbout}
                    >
                      About Us
                    </a>
                  </li>
                )}

                {this.state.isLoggedIn == 1 && (
                  <li className="nav-item">
                    <a
                      className="nav-link profile-pic dj-img"
                      onClick={this.redirectToProfile}
                    >
                      <span className="dj-pic">
                        <img
                          src={
                            userProfile
                              ? userProfile
                              : "https://nwsid.net/wp-content/uploads/2015/05/dummy-profile-pic-300x300.png"
                          }
                        />
                      </span>

                      <span className="userName">{userName}</span>
                    </a>
                  </li>
                )}

                {this.state.isLoggedIn == 1 && (
                  <li className="nav-item ">
                    <a
                      className="nav-link about-us"
                      onClick={this.redirectToAbout}
                    >
                      About Us
                    </a>
                  </li>
                )}

                {this.state.isLoggedIn == 0 && (
                  <li className="nav-item ">
                    <a
                      className="nav-link login-button"
                      onClick={this.redirectToLogin}
                    >
                      {"Login  "}
                      {/*<span className="bar">|</span>*/}
                    </a>
                  </li>
                )}
                {this.state.isLoggedIn == 1 && (
                  <li className="nav-item">
                    <a
                      className="nav-link login-button logout-btn"
                      onClick={this.logoutMe}
                    >
                      {"Logout"}
                      {/*<i className="fa fa-cog" aria-hidden="true"></i>*/}
                      {/*<span className="bar">|</span>*/}
                    </a>
                  </li>
                )}
                {/*<li className="nav-item">
                  <a
                    className="nav-link map"
                    onClick={this.props.showLocationModal}
                  >
                    <i className="fa fa-map-marker" aria-hidden="true"></i>
                  </a>
                </li>*/}
                {this.state.isLoggedIn == 0 && (
                  <li className="nav-item">
                    <a className="nav-link dj-btn" onClick={this.redirectToDj}>
                      {"Are you a DJ?"}
                    </a>
                  </li>
                )}
              </ul>
            </form>
          </div>
        </nav>
      </div>
    );
  }
}
export default withRouter(Header);
