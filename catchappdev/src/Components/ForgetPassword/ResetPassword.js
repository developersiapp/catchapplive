import React, { Component } from "react";

import validator from "validator";
import { connect } from "react-redux";
import { bindActionCreators } from "redux";
import {
  resPassword,
  userResPassword,
  exportEmptyData,
} from "../../Actions/loginAction.js";
import { withRouter } from "react-router";
import LoadingOverlay from "react-loading-overlay";
import ScaleLoader from "react-spinners/ScaleLoader";
import { toast } from "react-toastify";
import { isFormSubmit } from "../../Utils/services.js";

class ResetPassword extends Component {
  constructor(props) {
    const userType = localStorage.getItem("user-type");
    super(props);
    this.state = {
      passError: "",
      errorMessagePass: "",
      password: "",
      confirmPassword: "",
      token: this.props.match.params.token ? this.props.match.params.token : "",
      showLoader: false,
      userType: userType ? userType : "",
      userData: {},
    };
  }

  componentDidMount() {
    if (!this.state.token) {
      //console.log(this.state.token);
      window.location.href = `/`;
    }
  }

  handleInputChange = (event) => {
    const target = event.target;
    const value = target.type === "checkbox" ? target.checked : target.value;
    this.setState({
      [event.target.name]: value,
      errorMessagePass: "",
    });
  };

  static getDerivedStateFromProps(props, state) {
    let returnState = {};
    if (props.showLoader != undefined && props.showLoader == false) {
      return { showLoader: false };
    }
    if (props.userData !== undefined && props.userData !== state.userData) {
      returnState.showLoader = false;
      toast.success(props.userData.message);
      if (state.userType == "dj") {
        props.history.push(`/dj-login`);
      }
      if (state.userType == "user") {
        props.history.push(`/user-login`);
      }
    }
    if (props.errorData !== undefined && props.errorData !== state.errorData) {
      props.exportEmptyData();
      toast.error("Server error, Please try again!");
      returnState.showLoader = false;
      return returnState;
    }

    return null;
  }

  handleSubmit = (event) => {
    event.preventDefault();
    const userType = localStorage.getItem("user-type");

    if (isFormSubmit()) {
      //====Frontend validation=================
      let error = false;
      this.setState({ passError: "", errorMessagePass: "" });

      if (
        typeof this.state.password === undefined ||
        this.state.password === null ||
        this.state.password === ""
      ) {
        this.setState({
          //errorMessagePass: "Password can not be blank!",
          passError: "fieldError",
        });
        error = true;
        return;
      } else {
        error = false;
        this.setState({
          errorMessagePass: "",
          passError: "",
        });
      }

      if (
        typeof this.state.confirmPassword === undefined ||
        this.state.confirmPassword === null ||
        this.state.confirmPassword === ""
      ) {
        this.setState({
          //errorMessagePass: "Confirm Password can not be blank!",
          conPassError: "fieldError",
        });
        error = true;
        return;
      } else {
        error = false;
        this.setState({
          errorMessagePass: "",
          conPassError: "",
        });
      }

      if (this.state.confirmPassword !== this.state.password) {
        toast.error("Password not matched!");
        error = true;
        this.setState({ conPassError: "fieldError", passError: "fieldError" });
        return;
      } else {
        error = false;
        this.setState({
          errorMessagePass: "",
          passError: "",
        });
      }

      if (this.state.password && this.state.password.length < 8) {
        toast.error("Passwords must be at least 8 characters");
        error = true;
        this.setState({
          //errorMessagePass: "Password can not be blank!",
          passError: "fieldError",
        });
        return;
      }

      if (error === true) {
        this.setState({ loggingIn: false });
        return;
      }
      //======End frontend validation=========

      let formData = {
        password: this.state.password,
        token: this.props.match.params ? this.props.match.params.token : "",
      };
      this.setState({ showLoader: true });
      if (userType == "dj") {
        this.props.resPassword(formData);
      } else {
        this.props.userResPassword(formData);
      }
    }
  };

  redirectLogin = () => {
    return <div>{this.props.history.push(`/`)}</div>;
  };

  render() {
    return (
      <div>
        <LoadingOverlay
          active={this.state.showLoader}
          spinner={<ScaleLoader color={"#fb556b"} />}
          text={"Loading"}
        >
          {" "}
          <div className="row">
            <div className="col-12 col-sm-12 col-xl-6 px-0 left-text">
              <section className="login-left-box">
                <div className="login-box-inner">
                  <div className="display-block logo-box mb-4">
                    <img
                      src={
                        this.state.userType == "dj"
                          ? "img/logo-color.png"
                          : "img/logo-color-2.png"
                      }
                    />
                    <span className="catch">
                      {this.state.userType == "dj" ? "Catch Dj" : "CatchApp"}
                    </span>
                  </div>

                  <div className="display-block logo-box mb-4 text-center">
                    <span>Reset Password</span>
                  </div>
                  <form onSubmit={this.handleSubmit}>
                    <div className="display-block">
                      <span
                        className={
                          this.state.passError
                            ? "input-border fieldError"
                            : "input-border"
                        }
                      >
                        <i className="fa fa-lock" aria-hidden="true"></i>
                        <input
                          type="password"
                          placeholder="New Password"
                          name="password"
                          value={this.state.password}
                          onChange={this.handleInputChange}
                        />
                      </span>
                    </div>
                    <div className="display-block">
                      <span
                        className={
                          this.state.conPassError
                            ? "input-border fieldError"
                            : "input-border"
                        }
                      >
                        <i className="fa fa-lock" aria-hidden="true"></i>
                        <input
                          type="password"
                          placeholder="Confirm Password"
                          name="confirmPassword"
                          value={this.state.confirmPassword}
                          onChange={this.handleInputChange}
                        />
                      </span>
                    </div>

                    <div className="display-block">
                      <div className="row">
                        <div className="col-xl-12 col-sm-12 col-12 text-center">
                          <button type="submit" className="btn login-btn">
                            Reset Password
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>
                  <hr></hr>
                  <div className="display-block">
                    <div className="row">
                      <div className="col-xl-12 col-sm-12 col-12 register-txt text-center">
                        <a onClick={this.redirectLogin}>Login</a>
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
        </LoadingOverlay>
      </div>
    );
  }
}
const mapStateToProps = (state) => {
  if (state.LoginReducer.action === "RESET_PASSWORD") {
    const returnState = {};
    if (state.LoginReducer.data.error !== false) {
      returnState.errorData = state.LoginReducer.data;
      returnState.errorDate = new Date();
    } else {
      returnState.userData = state.LoginReducer.data;
      returnState.userDate = new Date();
    }
    return returnState;
  } else if (state.LoginReducer.action === "USER_RESET_PASSWORD") {
    const returnState = {};
    if (state.LoginReducer.data.error !== false) {
      returnState.errorData = state.LoginReducer.data;
      returnState.errorDate = new Date();
    } else {
      returnState.userData = state.LoginReducer.data;
      returnState.userDate = new Date();
    }
    return returnState;
  } else {
    return {
      loginMessage: "",
    };
  }
};

const mapDispatchToProps = (dispatch) => {
  return bindActionCreators(
    {
      resPassword: resPassword,
      userResPassword: userResPassword,
      exportEmptyData: exportEmptyData,
    },
    dispatch
  );
};
export default connect(
  mapStateToProps,
  mapDispatchToProps
)(withRouter(ResetPassword));
