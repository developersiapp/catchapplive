import React, { Component } from "react";
import validator from "validator";
import { connect } from "react-redux";
import { bindActionCreators } from "redux";
import {
  forgetPassword,
  userForgetPassword,
  exportEmptyData,
} from "../../Actions/loginAction.js";
import { withRouter } from "react-router";
import LoadingOverlay from "react-loading-overlay";
import ScaleLoader from "react-spinners/ScaleLoader";
import { toast } from "react-toastify";
import { isFormSubmit } from "../../Utils/services.js";

class ForgetPassword extends Component {
  constructor(props) {
    super(props);
    const userType = localStorage.getItem("user-type");
    this.state = {
      emailError: "",
      errorMessageEmail: "",
      userType: userType,
      showLoader: false,
      email: "",
      userType: userType ? userType : "",
    };
  }

  componentDidMount() {
    if (this.state.userType == "") {
      this.props.history.push(`/`);
    }
  }

  handleInputChange = (event) => {
    const target = event.target;
    const value = target.value;

    this.setState({
      [event.target.name]: value,
    });
  };

  static getDerivedStateFromProps(props, state) {
    props.exportEmptyData();
    let returnState = {};
    if (props.showLoader != undefined && props.showLoader == false) {
      return { showLoader: false };
    }
    if (props.userData !== undefined && props.userData !== state.userData) {
      returnState.userDate = props.userDate;
      returnState.errorMessageEmail = props.userData ? props.userData.data : "";
      returnState.successMessage = props.userData ? props.userData.message : "";
      if (returnState.errorMessageEmail) {
        toast.success(returnState.errorMessageEmail);
        returnState.email = "";
        props.history.push(`/dj-login`);
      }
      if (returnState.successMessage) {
        returnState.email = "";
        toast.success(returnState.successMessage);
        props.history.push(`/user-login`);
      }
      returnState.showLoader = false;
      //props.history.push("/login");
      return returnState;
    }
    if (
      props.errorData !== undefined &&
      props.errorData.error == true &&
      props.errorData !== state.errorData
    ) {
      returnState.errorMessageEmail = props.errorData.message
        ? props.errorData.message
        : "";
      returnState.showLoader = false;

      toast.error(returnState.errorMessageEmail);
      returnState.errorDate = props.errorDate;

      return returnState;
    }
    return null;
  }

  handleSubmit = (event) => {
    event.preventDefault();
    if (isFormSubmit()) {
      //====Frontend validation=================
      let error = false;
      this.setState({ loggingIn: true });

      if (
        typeof this.state.email == undefined ||
        this.state.email == null ||
        this.state.email.trim() == ""
      ) {
        this.setState({
          //errorMessageEmail: "Email can not be blank!",
          emailError: "fieldError",
        });
        error = true;
        return;
      } else if (!validator.isEmail(this.state.email)) {
        error = true;
        this.setState({
          //errorMessageEmail: "Incorrect email address",
          emailError: "fieldError",
        });
        toast.error("Incorrect email address!");
        return;
      }

      if (error === true) {
        this.setState({ loggingIn: false });
        return;
      }
      //======End frontend validation=========

      let formData = {
        email: this.state.email,
        web: true,
      };
      this.setState({ showLoader: true });
      if (this.state.userType == "dj") {
        this.props.forgetPassword(formData);
      }
      if (this.state.userType == "user") {
        this.props.userForgetPassword(formData);
      }
    }
  };

  redirectLogin = () => {
    if (this.state.userType == "user") {
      return <div>{this.props.history.push(`/user-login`)}</div>;
    }
    if (this.state.userType == "dj")
      return <div>{this.props.history.push(`/dj-login`)}</div>;
  };

  redirectRegister = () => {
    if (this.state.userType == "user") {
      return <div>{this.props.history.push(`/register`)}</div>;
    }
    if (this.state.userType == "dj") {
      return <div>{this.props.history.push(`/sign-up`)}</div>;
    }
  };

  redirectToDashboard = () => {
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
          <div className="row">
            <div className="col-12 col-sm-12 col-xl-6 col-md-12 col-lg-6 px-0 left-text">
              <section className="login-left-box">
                <div className="login-box-inner">
                  <div className="display-block logo-box mb-4">
                    <img
                      src={
                        this.state.userType == "dj"
                          ? "img/logo-color.png"
                          : "img/logo-color-2.png"
                      }
                      onClick={this.redirectToDashboard}
                    />
                    <span className="catch">
                      {this.state.userType == "dj" ? "Catch Dj" : "CatchApp"}
                    </span>
                  </div>

                  <div className="display-block logo-box mb-4">
                    <span>
                      Enter your email and we'll send you a link to get back
                      into your account.
                    </span>
                  </div>
                  <form onSubmit={this.handleSubmit}>
                    <div className="row">
                      <div className="col-12">
                        <div className="display-block">
                          <span
                            className={
                              this.state.emailError
                                ? "input-border fieldError"
                                : "input-border"
                            }
                          >
                            <i
                              className="fa fa-envelope"
                              aria-hidden="true"
                            ></i>
                            <input
                              type="email"
                              placeholder="Email"
                              name="email"
                              value={this.state.email}
                              onChange={this.handleInputChange}
                            />
                          </span>
                        </div>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-12">
                        <div className="display-block">
                          <div className="row">
                            <div className="col-xl-12 col-sm-12  col-12 text-center send-link">
                              <button type="submit" className="btn login-btn">
                                SEND LINK
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                  <hr></hr>
                  <div className="display-block">
                    <div className="row">
                      <div className="col-xl-6 col-sm-6 col-6 register-txt">
                        <a onClick={this.redirectRegister}>Register now</a>
                      </div>
                      <div className="col-xl-6 col-sm-6 col-6 register-txt text-right">
                        <a onClick={this.redirectLogin}>Login</a>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
            </div>

            <div className="col-12 col-sm-12 col-xl-6 col-md-12 col-lg-6 px-0 right-img">
              <section className="login-right-box forget-image">
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
  const returnState = {};
  if (state.LoginReducer.action === "FORGET_PASSWORD") {
    if (state.LoginReducer.data.error !== false) {
      returnState.errorData = state.LoginReducer.data;
      returnState.errorDate = new Date();
    } else {
      //const userData = state.LoginReducer.data.data;
      returnState.userData = state.LoginReducer.data;
      returnState.userDate = new Date();
    }
    return returnState;
  }
  if (state.LoginReducer.action === "USER_FORGET_PASSWORD") {
    if (state.LoginReducer.data.error !== false) {
      returnState.errorData = state.LoginReducer.data;
      returnState.errorDate = new Date();
    } else {
      //const userData = state.LoginReducer.data.data;
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
      userForgetPassword: userForgetPassword,
      forgetPassword: forgetPassword,
      exportEmptyData: exportEmptyData,
    },
    dispatch
  );
};
export default connect(
  mapStateToProps,
  mapDispatchToProps
)(withRouter(ForgetPassword));
