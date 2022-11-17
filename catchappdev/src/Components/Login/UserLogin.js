import React, { Component } from "react";
import "./login.css";
import validator from "validator";
import { connect } from "react-redux";
import { bindActionCreators } from "redux";
import {
  userSignInRequest,
  userSocialLogin,
  exportEmptyData,
  userCheckEmail,
  userRegister,
  userSocialRegister,
} from "../../Actions/loginAction.js";
import { withRouter } from "react-router";
import FacebookLogin from "react-facebook-login";
import Geocode from "react-geocode";
import LoadingOverlay from "react-loading-overlay";
import { isFormSubmit } from "../../Utils/services.js";
import ScaleLoader from "react-spinners/ScaleLoader";
import { ToastContainer, toast } from "react-toastify";
import GoogleLogin from "react-google-login";

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

    // set response language. Defaults to english.
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
          localStorage.setItem("location", "Select Location");
        } else {
          localStorage.setItem("location", city);
        }
      },
      (error) => {
        // console.error(error);
      }
    );

    // Get latidude & longitude from address.
    /*Geocode.fromAddress("Eiffel Tower").then(
      (response) => {
        const { lat, lng } = response.results[0].geometry.location;
        // console.log(lat, lng);
      },
      (error) => {
        console.error(error);
      }
    );*/

    localStorage.setItem("placeData", JSON.stringify(placeData));
  });
} else {
  console.error("Geolocation is not supported by this browser!");
}

class UserLogin extends Component {
  constructor(props) {
    const location = localStorage.getItem("location");
    const sessionEmail = localStorage.getItem("userEmail");
    const session = localStorage.getItem("userSession");
    const userType = localStorage.getItem("user-type");
    const userData = JSON.parse(localStorage.getItem("userData"));
    const isLoggedIn = JSON.parse(localStorage.getItem("isLoggedIn"));

    super(props);
    this.state = {
      email: sessionEmail ? sessionEmail : "",
      password: "",
      emailError: "",
      passError: "",
      passwordError: "",
      loggingIn: false,
      errorMessageEmail: "",
      errorMessagePass: "",
      location: {},
      rememberMe: session ? session : false,
      showLoader: false,
      showMe: false,
      emailFound: "",
      location: location ? location : "Select Location",
      userType: userType ? userType : "",
      logged_in: userData ? userData.logged_in : false,
      isLoggedIn: isLoggedIn ? isLoggedIn : 0,
      userId: userData ? userData.id : "",
    };
  }

  redirectDashboard = () => {
    return <div>{this.props.history.push(`/dashboard`)}</div>;
  };

  redirectUserDashboard = () => {
    return <div>{this.props.history.push(`/`)}</div>;
  };

  componentDidMount() {
    if (
      (this.state.userType =
        "dj" && (this.state.logged_in == true || this.state.isLoggedIn == 1))
    ) {
      this.redirectDashboard();
    }

    if (
      (this.state.userType =
        "user" && (this.state.logged_in == true || this.state.isLoggedIn == 1))
    ) {
      return <div>{this.props.history.push(`/`)}</div>;
    }
  }

  static getDerivedStateFromProps(props, state) {
    props.exportEmptyData();
    let returnState = {};
    if (props.userData !== undefined && props.userData !== state.userData) {
      returnState.errorMessagePass = "";
      returnState.passError = "";
      returnState.showLoader = false;
      if (state.rememberMe == true) {
        localStorage.setItem("userEmail", props.userData.email);
        localStorage.setItem("userSession", state.rememberMe);
      }
      props.history.push("/");
      return returnState;
    }

    if (
      props.errorData !== undefined &&
      props.errorData.error == true &&
      props.errorData !== state.errorData
    ) {
      returnState.errorMessagePass = props.errorData.message
        ? props.errorData.message
        : "";
      returnState.showLoader = false;
      toast.error(returnState.errorMessagePass);
      return returnState;
    }
    if (props.emailData !== undefined && props.emailData !== state.emailData) {
      returnState.emailFound = props.emailData ? props.emailData.status : "";
      returnState.showLoader = false;
      if (props.emailData.status == 0 || props.emailData.status == 1) {
        returnState.showMe = true;
      }
      if (props.emailData.status == 2) {
        returnState.showMe = false;
        let formData = {
          client_id: state.clientId ? state.clientId : "",
          first_name: state.fbFirstname ? state.fbFirstname : "",
          last_name: state.fbLastname ? state.fbLastname : "",
          email: props.emailData.email ? props.emailData.email : "",
          register_type: "2",
          oauth_key: state.oauthKey ? state.oauthKey : "",
          profile_picture_url: state.fbPictureUrl ? state.fbPictureUrl : "",
          location: state.location,
          user_name: props.emailData.email ? props.emailData.email : "",
          //gender: "",
          web: true,
          device_token: "ABCDEF",
        };
        returnState.showLoader = true;
        props.userSocialRegister(formData);
      }
      return returnState;
    }
    return null;
  }

  handleInputChange = (event) => {
    const target = event.target;
    const value = target.type === "checkbox" ? target.checked : target.value;
    this.setState({
      [event.target.name]: value,
      errorMessagePass: "",
    });
  };

  handleSubmit = (event) => {
    event.preventDefault();
    let notEmail = false;
    let formData = {};
    if (isFormSubmit()) {
      //====Frontend validation=================
      let error = false;
      this.setState({ emailError: "", passwordError: "", loggingIn: true });

      if (
        typeof this.state.email == undefined ||
        this.state.email == null ||
        this.state.email == ""
      ) {
        this.setState({
          //errorMessageEmail: "Email can not be blank!",
          emailError: "fieldError",
        });
        error = true;
      } else if (!validator.isEmail(this.state.email)) {
        notEmail = true;
        /*error = true;
        this.setState({
          //errorMessageEmail: "Incorrect email address",
          emailError: "fieldError",
        });
        toast.error("Incorrect email address");
        return;*/
      }
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
      } else {
        error = false;
        this.setState({
          errorMessagePass: "",
          passError: "",
        });
      }

      if (error === true) {
        this.setState({ loggingIn: false });
        return;
      }
      //======End frontend validation=========

      if (notEmail) {
        formData = {
          user_name: this.state.email,
          password: this.state.password,
          device_token: "ABCDEF",
        };
      } else {
        formData = {
          email: this.state.email,
          password: this.state.password,
          device_token: "ABCDEF",
        };
      }

      // localStorage.setItem("location", this.state.location);
      if (this.state.rememberMe == true) {
        localStorage.setItem("userEmail", this.state.email);
        localStorage.setItem("userSession", this.state.rememberMe);
      }
      if (this.state.rememberMe == false) {
        localStorage.removeItem("userEmail");
        localStorage.removeItem("userSession");
      }
      this.setState({ showLoader: true });
      this.props.userSignInRequest(formData);
    }
  };

  redirectForgetPassword = () => {
    return <div>{this.props.history.push(`/forget-password`)}</div>;
  };

  redirectSignup = () => {
    return <div>{this.props.history.push(`/register`)}</div>;
  };

  handleFbSubmit(e) {
    e.preventDefault();
    if (
      this.state.fbEmail == "" ||
      this.state.fbEmail == undefined ||
      this.state.fbEmail == null ||
      this.state.fbEmail.trim() == ""
    ) {
      this.setState({ fbEmailErorr: "fieldError" });
      return;
    }

    let formData = {
      client_id: this.state.clientId ? this.state.clientId : "",
      first_name: this.state.fbFirstname ? this.state.fbFirstname : "",
      last_name: this.state.fbLastname ? this.state.fbLastname : "",
      email: this.state.fbEmail ? this.state.fbEmail : "",
      register_type: "2",
      oauth_key: this.state.oauthKey ? this.state.oauthKey : "",
      profile_picture_url: this.state.fbPictureUrl
        ? this.state.fbPictureUrl
        : "",
      location: this.state.location,
      user_name: this.state.fbEmail ? this.state.fbEmail : "",
      //gender: "female",
      web: true,
      device_token: "ABCDEF",
    };
    this.setState({ showLoader: true });
    this.props.userSocialRegister(formData);
  }
  /*handleFBLogin = (response) => {
    if (isFormSubmit()) {
      let formData = {};

      if (response) {
        formData = {
          client_id: response.userID ? response.userID : "",
          //email: response.email ? response.email : "",
          registration_type: "2",
          device_token: "ABCDEF",
          web: true,
        };
        this.setState({ showLoader: true });
        this.props.userSocialLogin(formData);
      } else {
        toast.error("Facebook error, please try again later!");
      }
    }
  };*/

  handleFBLogin = (response) => {
    let formData = {};
    if (response) {
      if (
        response.email == "" ||
        response.email == undefined ||
        response.email == null
      ) {
        this.setState({
          //showMe: true,
          clientId: response.userID ? response.userID : "",
          fbFirstname: response.name
            ? response.name.split(" ").slice(0, -1).join(" ")
            : "",
          fbLastname: response.name
            ? response.name.split(" ").slice(-1).join(" ")
            : "",
          fbEmail: response.email ? response.email : this.state.fbEmail,
          oauthKey: response.accessToken ? response.accessToken : "",
          fbPictureUrl: response.picture ? response.picture.data.url : "",
          userName: response.email ? response.email : this.state.fbEmail,
          //fbGender: "",
        });
        formData = {
          client_id: response.userID ? response.userID : "",
          registration_type: "2",
        };
        this.setState({ showLoader: true });
        this.props.userCheckEmail(formData);
        return;
      }

      formData = {
        client_id: response.userID ? response.userID : "",
        first_name: response.name
          ? response.name.split(" ").slice(0, -1).join(" ")
          : "",
        last_name: response.name
          ? response.name.split(" ").slice(-1).join(" ")
          : "",
        email: response.email ? response.email : this.state.fbEmail,
        register_type: "2",
        oauth_key: response.accessToken ? response.accessToken : "",
        profile_picture_url: response.picture ? response.picture.data.url : "",
        location: this.state.location,
        user_name: response.email ? response.email : response.userID,
        //gender: "",
        device_token: "ABCDEF",
        web: true,
      };
      this.setState({ showLoader: true });
      this.props.userSocialRegister(formData);
    } else {
      toast.error("Facebook error, please try again later!");
    }
  };

  /*responseGoogle = (response) => {
    if (isFormSubmit()) {
      let formData = {};

      if (response) {
        formData = {
          client_id: response.googleId ? response.googleId : "",
          email: response.profileObj.email ? response.profileObj.email : "",
          registration_type: "3",
          web: true,
          device_token: "ABCDEF",
        };
        this.setState({ showLoader: true });
        this.props.userSocialLogin(formData);
      } else {
        toast.error("Google error, please try again later!");
      }
    }
  };*/

  responseGoogle = (response) => {
    if (isFormSubmit()) {
      let formData = {};
      if (response) {
        formData = {
          client_id: response.googleId ? response.googleId : "",
          first_name: response.profileObj
            ? response.profileObj.name.split(" ").slice(0, -1).join(" ")
            : "",
          last_name: response.profileObj
            ? response.profileObj.name.split(" ").slice(-1).join(" ")
            : "",
          email: response.profileObj ? response.profileObj.email : "",
          register_type: "3",
          oauth_key: response.accessToken ? response.accessToken : "",
          profile_picture_url: response.profileObj
            ? response.profileObj.imageUrl
            : "",
          location: this.state.location ? this.state.location : "",
          user_name: response.profileObj
            ? response.profileObj.email
            : response.profileObj.userID,
          // gender: "",
          web: true,
          device_token: "ABCDEF",
        };
        this.setState({ showLoader: true });
        this.props.userSocialRegister(formData);
      } else {
        toast.error("Google error, please try again later!");
      }
    }
  };

  redirectUserDashboard = () => {
    return <div>{this.props.history.push(`/`)}</div>;
  };

  failGoogle = () => {
    toast.error("Google error, please try again later!");
    this.setState({});
  };

  dismissModal = () => {
    this.setState({
      showMe: false,
      emailFound: "",
      clientId: "",
      fbName: "",
      fbEmail: "",
      oauthKey: "",
      fbPictureUrl: "",
      userName: "",
      fbGender: "",
      fbEmailErorr: "",
    });
    toast.error("Registration Cancelled!");
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
            <div className="col-12 col-sm-12 col-xl-6 px-0 left-text">
              <section className="login-left-box">
                <div className="login-box-inner">
                  <div className="display-block logo-box mb-4">
                    <img
                      src="img/logo-color-2.png"
                      onClick={this.redirectUserDashboard}
                    />
                    <span className="catch">CatchApp</span>
                  </div>
                  <form onSubmit={this.handleSubmit}>
                    <div className="display-block">
                      <span
                        className={
                          this.state.emailError
                            ? "input-border fieldError"
                            : "input-border"
                        }
                      >
                        <i className="fa fa-user" aria-hidden="true"></i>
                        <input
                          type="text"
                          placeholder="Username or email"
                          name="email"
                          value={this.state.email}
                          onChange={this.handleInputChange}
                        />
                      </span>
                    </div>
                    <div className="display-block">
                      <span
                        className={
                          this.state.passError
                            ? "input-border fieldError"
                            : "input-border"
                        }
                      >
                        <img src="img/lock.png" />
                        <input
                          type="password"
                          placeholder="Password"
                          name="password"
                          value={this.state.password}
                          onChange={this.handleInputChange}
                        />
                      </span>
                    </div>
                    <div className="display-block" style={{ clear: "left" }}>
                      <div className="row">
                        <div className="col-xl-6 col-sm-6 col-6 remembr-box">
                          <label className="container-checkbox">
                            <span className="rm-text">Remember me</span>
                            <input
                              type="checkbox"
                              name="rememberMe"
                              onChange={this.handleInputChange}
                              checked={
                                this.state.rememberMe ? "checked" : false
                              }
                            />
                            <span className="checkmark"></span>
                          </label>
                        </div>

                        <div className="col-xl-6 col-sm-6 col-6 login-btn-box">
                          <button
                            type="submit"
                            className="btn login-btn float-right"
                          >
                            LOGIN
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>
                  <div className="display-block">
                    <div className="row">
                      <div className="col-xl-6 col-sm-6 col-6 register-txt">
                        <a onClick={this.redirectSignup}>Register now</a>
                      </div>
                      <div className="col-xl-6 col-sm-6 col-6 forgot-txt text-right">
                        <a onClick={this.redirectForgetPassword}>
                          Forgot password?
                        </a>
                      </div>
                    </div>
                  </div>

                  <div className="display-block text-center or">
                    <span>or</span>
                  </div>

                  <div className="display-block">
                    {
                      <FacebookLogin
                        appId="2492883237611590"
                        autoLoad={false}
                        fields="name,email,picture"
                        callback={this.handleFBLogin}
                        cssClass="my-facebook-button-class btn btn-fb"
                        icon="fa-facebook"
                      />
                    }

                    {/*<button type="button" className="btn btn-fb">
                    <img src="img/f-icon.png" /> LOGIN WITH FACEBOOK
                  </button>*/}
                  </div>
                  <div className="display-block">
                    <GoogleLogin
                      clientId={
                        "38739005846-2mi6s38na46fb1lbes4ba9b8bko14eqg.apps.googleusercontent.com"
                      }
                      onSuccess={this.responseGoogle}
                      onFailure={this.failGoogle}
                      className="google-btn w-100 btn"
                      buttonText="Login with Google"
                    ></GoogleLogin>
                  </div>
                </div>
              </section>
            </div>

            <div className="col-12 col-sm-12 col-xl-6 px-0 right-img">
              <section className="login-right-box">
                <div className="login-box-inner">
                  <img src="img/right-img-min.png" />
                </div>
              </section>
            </div>
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
                        Email required for register
                      </h5>
                    </div>
                    <div className="modal-body">
                      <div className="display-block type-something">
                        <input
                          className={
                            this.state.fbEmailErorr
                              ? "form-control fieldError"
                              : "form-control"
                          }
                          placeholder="Email"
                          value={this.state.fbEmail}
                          onChange={this.handleInputChange}
                          name={"fbEmail"}
                        />
                      </div>
                    </div>
                    <div className="modal-footer modal-button">
                      <div className="add-club-btn">
                        <button
                          type="button"
                          className="btn mb-4"
                          onClick={(e) => this.handleFbSubmit(e)}
                        >
                          Submit
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            )}
          </div>
        </LoadingOverlay>
      </div>
    );
  }
}
const mapStateToProps = (state) => {
  const returnState = {};
  if (state.LoginReducer.action === "USER_LOGIN") {
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
  } else if (state.LoginReducer.action === "USER_SOCIAL_LOGIN") {
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
  }
  if (state.LoginReducer.action === "USER_CHECK_EMAIL") {
    if (state.LoginReducer.data == undefined) {
    } else {
      returnState.emailData = state.LoginReducer.data;
    }
    return returnState;
  }

  if (state.LoginReducer.action === "USER_SOCIAL_REGISTER") {
    if (state.LoginReducer.data.error !== false) {
      returnState.errorData = state.LoginReducer.data;
      returnState.errorDate = new Date();
    } else {
      const userData = state.LoginReducer.data.data;
      returnState.userData = state.LoginReducer.data.data;
      localStorage.setItem("userData", JSON.stringify(userData));
      localStorage.setItem("isLoggedIn", 1);
    }

    return returnState;
  }

  if (state.LoginReducer.action === "USER_REGISTER") {
    const returnState = {};

    if (state.LoginReducer.data.error !== false) {
      returnState.errorData = state.LoginReducer.data;
      returnState.errorDate = new Date();
    } else {
      const userData = state.LoginReducer.data.data;
      returnState.userData = state.LoginReducer.data.data;
      localStorage.setItem("userData", JSON.stringify(userData));
      localStorage.setItem("isLoggedIn", 1);
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
      userSignInRequest: userSignInRequest,
      userSocialLogin: userSocialLogin,
      exportEmptyData: exportEmptyData,
      userCheckEmail: userCheckEmail,
      userRegister: userRegister,
      userSocialRegister: userSocialRegister,
    },
    dispatch
  );
};
export default connect(
  mapStateToProps,
  mapDispatchToProps
)(withRouter(UserLogin));
