import React, { Component } from "react";
import "./login.css";
import validator from "validator";
import { connect } from "react-redux";
import { bindActionCreators } from "redux";
import { userSignInRequest } from "../../Actions/loginAction.js";
import { withRouter } from "react-router";
import FacebookLogin from "react-facebook-login";
import Geocode from "react-geocode";

class Home extends Component {
  constructor(props) {
    super(props);
    const userData = JSON.parse(localStorage.getItem("userData"));
    const isLoggedIn = JSON.parse(localStorage.getItem("isLoggedIn"));
    const userType = localStorage.getItem("user-type");
    this.state = {
      logged_in: userData ? userData.logged_in : false,
      isLoggedIn: isLoggedIn ? isLoggedIn : 0,
      userId: userData ? userData.id : "",
      userType: userType ? userType : ""
    };
  }

  componentDidMount() {
    if (this.state.userType == "dj") {
      if (this.state.logged_in == true && this.state.isLoggedIn == 1) {
        this.props.history.push(`/dashboard`);
      }
    }
    if (this.state.userType == "user") {
      if (this.state.logged_in == true && this.state.isLoggedIn == 1) {
        this.props.history.push(`/`);
      }
    }
  }
  static getDerivedStateFromProps(props, state) {
    return null;
  }

  handleInputChange = event => {
    const target = event.target;
    const value = target.type === "checkbox" ? target.checked : target.value;
    this.setState({
      [event.target.name]: value,
      errorMessagePass: ""
    });
  };

  handleSubmit = event => {
    event.preventDefault();

    //====Frontend validation=================
  };

  djLogin = () => {
    localStorage.setItem("user-type", "dj");
    return <div>{this.props.history.push(`/dj-login`)}</div>;
  };

  userLogin = () => {
    localStorage.setItem("user-type", "user");
    return <div>{this.props.history.push(`/user-login`)}</div>;
  };

  render() {
    return (
      <div>
        <div className="row">
          <div className="col-12 col-sm-12 col-xl-6 px-0 left-text">
            <section className="login-left-box">
              <div className="login-box-inner">
                <div className="display-block logo-box mb-4">
                  <img src="img/logo-color.png" />{" "}
                  <span className="catch">Catch Dj</span>
                </div>
                <div className="display-block">
                  <div className="row">
                    <div className="col-xl-6 col-sm-12 col-12 login-btn-box mt-4">
                      <button
                        type="button"
                        className="btn login-btn w-100"
                        onClick={this.djLogin}
                      >
                        DJ LOGIN
                      </button>
                    </div>

                    <div className="col-xl-6 col-sm-12 col-12 login-btn-box mt-4">
                      <button
                        type="button"
                        className="btn login-btn w-100"
                        onClick={this.userLogin}
                      >
                        USER LOGIN
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>

          <div className="col-12 col-sm-12 col-xl-6 px-0 right-img">
            <section className="login-right-box">
              <div className="login-box-inner">
                <img src="img/right-img.png" />
              </div>
            </section>
          </div>
        </div>
      </div>
    );
  }
}
const mapStateToProps = state => {
  if (state.LoginReducer.action === "LOGIN") {
    const returnState = {};
    if (state.LoginReducer.Logindata.error !== false) {
      returnState.errorData = state.LoginReducer.Logindata;
      returnState.errorDate = new Date();
    } else {
      const userData = state.LoginReducer.Logindata.data;
      returnState.userData = state.LoginReducer.Logindata.data;
      localStorage.setItem("userData", JSON.stringify(userData));
      localStorage.setItem("isLoggedIn", 1);
    }
    return returnState;
  } else {
    return {
      loginMessage: ""
    };
  }
};

const mapDispatchToProps = dispatch => {
  return {
    // same effect
    userSignInRequest: bindActionCreators(userSignInRequest, dispatch)
  };
};
export default connect(mapStateToProps, mapDispatchToProps)(withRouter(Home));
