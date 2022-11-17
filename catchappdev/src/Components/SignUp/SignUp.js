import React, { Component } from "react";
import validator from "validator";
import { connect } from "react-redux";
import { bindActionCreators } from "redux";
import {
  userSignUpRequest,
  exportEmptyData,
  djSocialRegister,
  checkEmail,
} from "../../Actions/loginAction.js";
import { withRouter } from "react-router";
import FacebookLogin from "react-facebook-login";
import Geocode from "react-geocode";
import { register } from "../../serviceWorker.js";
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import { toast } from "react-toastify";
import { isFormSubmit } from "../../Utils/services.js";
import ScaleLoader from "react-spinners/ScaleLoader";
import LoadingOverlay from "react-loading-overlay";
import GoogleLogin from "react-google-login";

class Signup extends Component {
  constructor(props) {
    super(props);
    localStorage.setItem("user-type", "dj");
    this.state = {
      email: "",
      password: "",
      emailError: "",
      passError: "",
      nameError: "",
      passwordError: "",
      loggingIn: false,
      errorMessageEmail: "",
      errorMessagePass: "",
      errorMessageName: "",
      name: "",
      userGender: "male",
      birthday: "",
      location: "",
      userName: "",
      userNameError: "",
      errorMessageUserName: "",
      birthdayError: "",
      errorMessageBirthday: "",
      showLoader: false,
      profileImage: "",
      showMe: false,
      fbEmail: "",
      emailFound: "",
    };
  }

  componentDidMount() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition((position) => {
        let data = position.coords;
        // console.log(data);
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
            const address = response.results[0].formatted_address;
            const data = address.split(",");
            //console.log(data);
            const city = data[data.length - 3];
            this.setState({ location: city });
            //console.log(address);
          },
          (error) => {
            // console.error(error);
          }
        );

        // Get latidude & longitude from address.
        /*Geocode.fromAddress("Eiffel Tower").then(
          (response) => {
            const { lat, lng } = response.results[0].geometry.location;
            //console.log(lat, lng);
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
  }
  static getDerivedStateFromProps(props, state) {
    props.exportEmptyData();
    let returnState = {};
    if (props.userData !== undefined && props.userData !== state.userData) {
      returnState.userDate = props.userDate;
      returnState.showMe = false;

      props.history.push("/dashboard");
      return returnState;
    }
    if (
      props.errorData !== undefined &&
      props.errorData.error == true &&
      props.errorData !== state.errorData &&
      props.errorDate !== state.errorDate
    ) {
      returnState.errorMessageEmail = props.errorData.message
        ? props.errorData.message
        : "";
      toast.error(returnState.errorMessageEmail);
      returnState.errorDate = props.errorDate;
      returnState.showLoader = false;
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
          name: state.fbName ? state.fbName : "",
          email: props.emailData.email ? props.emailData.email : "",
          registeration_type: "2",
          oauth_key: state.oauthKey ? state.oauthKey : "",
          profile_picture_url: state.fbPictureUrl ? state.fbPictureUrl : "",
          location: "",
          user_name: props.emailData.email ? props.emailData.email : "",
          // gender: "",
          web: true,
        };
        returnState.showLoader = true;
        props.djSocialRegister(formData);
      }
      return returnState;
    }

    return null;
  }

  handleInputChange = (event) => {
    //this.props.exportEmptyData();
    const target = event.target;
    let value = target.value;
    switch (target.type) {
      case "checkbox": {
        value = target.checked;
        break;
      }

      case "radio": {
        value = target.value == "male" ? "male" : "female";
        break;
      }
    }
    this.setState({ [event.target.name]: value });
  };

  handleSubmit = (event) => {
    event.preventDefault();
    if (isFormSubmit()) {
      //====Frontend validation=================
      let error = false;
      this.setState({
        emailError: "",
        passwordError: "",
        userNameError: "",
        loggingIn: true,
      });

      if (
        typeof this.state.name === undefined ||
        this.state.name === null ||
        this.state.name === ""
      ) {
        this.setState({
          // errorMessageName: "Name can not be blank!",
          nameError: "fieldError",
        });
        error = true;
      } else {
        error = false;
        this.setState({
          errorMessageName: "",
          nameError: "",
        });
      }

      if (
        typeof this.state.email == undefined ||
        this.state.email == null ||
        this.state.email == ""
      ) {
        this.setState({
          emailError: "fieldError",
        });
        error = true;
      } /*else if (!validator.isEmail(this.state.email)) {
        toast.error("Incorrect email address!");
        this.setState({
          emailError: "fieldError",
        });
        error = true;
        return;
      }*/

      if (
        typeof this.state.userName === undefined ||
        this.state.userName === null ||
        this.state.userName === ""
      ) {
        this.setState({
          //errorMessageUserName: "Username can not be blank!",
          userNameError: "fieldError",
        });
        error = true;
      } else {
        error = false;
        this.setState({
          errorMessageUserName: "",
          userNameError: "",
        });
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

      if (
        typeof this.state.birthday === undefined ||
        this.state.birthday === null ||
        this.state.birthday === ""
      ) {
        this.setState({
          //rrorMessageBirthday: "Date of Birth can not be blank!",
          birthdayError: "fieldError",
        });
        error = true;
      } else {
        error = false;
        this.setState({
          errorMessageBirthday: "",
          birthdayError: "",
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
      if (this.state.userName && this.state.userName.length < 6) {
        toast.error("Username must be at least 6 characters");
        error = true;
        this.setState({
          //errorMessagePass: "Password can not be blank!",
          userNameError: "fieldError",
        });
        return;
      }

      if (error === true) {
        this.setState({ loggingIn: false });
        return;
      }

      if (!validator.isEmail(this.state.email)) {
        toast.error("Incorrect email address!");
        this.setState({
          emailError: "fieldError",
        });
        error = true;
        return;
      }
      //======End frontend validation=========

      let formData = {
        email: this.state.email,
        password: this.state.password,
        name: this.state.name,
        user_name: this.state.userName,
        birth_date: this.state.birthday,
        gender: this.state.userGender,
        registeration_type: "7",
        device_token: "ABCDEF",
      };
      if (error == false) {
        localStorage.setItem(
          "location",
          JSON.stringify(this.state.location.trim())
        );
        this.setState({ showLoader: true });
        this.props.userSignUpRequest(formData);
      }
    }
  };

  handleChange = (date) => {
    this.setState({
      birthday: date,
    });
  };

  /*handleFbSubmit(e) {
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
      name: this.state.fbName ? this.state.fbName : "",
      email: this.state.fbEmail ? this.state.fbEmail : "",
      registeration_type: "2",
      oauth_key: this.state.oauthKey ? this.state.oauthKey : "",
      profile_picture_url: this.state.fbPictureUrl
        ? this.state.fbPictureUrl
        : "",
      location: this.state.location,
      user_name: this.state.fbEmail ? this.state.fbEmail : "",
      gender: "female",
      web: true,
    };
    this.setState({ showLoader: true });
    this.props.userSignUpRequest(formData);
  }*/

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
          fbName: response.name ? response.name : "",
          fbEmail: response.email ? response.email : this.state.fbEmail,
          oauthKey: response.accessToken ? response.accessToken : "",
          fbPictureUrl: response.picture ? response.picture.data.url : "",
          user_name: response.email ? response.email : this.state.fbEmail,
          fbGender: "",
        });
        formData = {
          client_id: response.userID ? response.userID : "",
          registration_type: "2",
        };
        this.setState({ showLoader: true });
        this.props.checkEmail(formData);
        return;
      }

      formData = {
        client_id: response.userID ? response.userID : "",
        name: response.name ? response.name : "",
        email: response.email ? response.email : this.state.fbEmail,
        registeration_type: "2",
        oauth_key: response.accessToken ? response.accessToken : "",
        profile_picture_url: response.picture ? response.picture.data.url : "",
        location: this.state.location,
        user_name: response.email ? response.email : response.userID,
        //gender: "",
        web: true,
      };
      this.setState({ showLoader: true });
      this.props.djSocialRegister(formData);
    } else {
      toast.error("Facebook error, please try again later!");
    }
  };

  fbSubmit = () => {
    let formData = {
      client_id: this.state.clientId ? this.state.clientId : "",
      name: this.state.fbName ? this.state.fbName : "",
      email: this.state.fbEmail ? this.state.fbEmail : "",
      registeration_type: "2",
      oauth_key: this.state.oauthKey ? this.state.oauthKey : "",
      profile_picture_url: this.state.fbPictureUrl
        ? this.state.fbPictureUrl
        : "",
      location: this.state.location,
      user_name: this.state.fbEmail ? this.state.fbEmail : "",
      //gender: "",
      web: true,
    };
    this.setState({ showLoader: true, emailFound: "" });
    this.props.djSocialRegister(formData);
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
      name: this.state.fbName ? this.state.fbName : "",
      email: this.state.fbEmail ? this.state.fbEmail : "",
      registeration_type: "2",
      oauth_key: this.state.oauthKey ? this.state.oauthKey : "",
      profile_picture_url: this.state.fbPictureUrl
        ? this.state.fbPictureUrl
        : "",
      location: this.state.location,
      user_name: this.state.fbEmail ? this.state.fbEmail : "",
      //gender: "",
      web: true,
    };
    this.setState({ showLoader: true });
    this.props.djSocialRegister(formData);
  }

  /*handleFBLogin = (response) => {
    let formData = {};
    if (response) {
      if (
        response.email == "" ||
        response.email == undefined ||
        response.email == null
      ) {
        this.setState({
          showMe: true,
          clientId: response.userID ? response.userID : "",
          fbName: response.name ? response.name : "",
          fbEmail: response.email ? response.email : this.state.fbEmail,
          oauthKey: response.accessToken ? response.accessToken : "",
          fbPictureUrl: response.picture ? response.picture.data.url : "",
          user_name: response.email ? response.email : this.state.fbEmail,
          fbGender: "female",
        });
        return;
      }

      formData = {
        client_id: response.userID ? response.userID : "",
        name: response.name ? response.name : "",
        email: response.email ? response.email : this.state.fbEmail,
        registeration_type: "2",
        oauth_key: response.accessToken ? response.accessToken : "",
        profile_picture_url: response.picture ? response.picture.data.url : "",
        location: this.state.location,
        user_name: response.email ? response.email : response.userID,
        gender: "female",
        web: true,
      };
      this.setState({ showLoader: true });
      this.props.userSignUpRequest(formData);
    } else {
      toast.error("Facebook error, please try again later!");
    }
  };*/

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

  redirectToLogin = () => {
    return <div>{this.props.history.push(`/dj-login`)}</div>;
  };

  responseGoogle = (response) => {
    if (isFormSubmit()) {
      let formData = {};

      if (response) {
        formData = {
          client_id: response.googleId ? response.googleId : "",
          name: response.profileObj ? response.profileObj.name : "",
          email: response.profileObj ? response.profileObj.email : "",
          registeration_type: "3",
          oauth_key: response.accessToken ? response.accessToken : "",
          profile_picture_url: response.profileObj
            ? response.profileObj.imageUrl
            : "",
          location: this.state.location,
          user_name: response.profileObj
            ? response.profileObj.email
            : response.profileObj.userID,
          //gender: "",
          web: true,
        };
        this.setState({ showLoader: true });
        this.props.djSocialRegister(formData);
      } else {
        toast.error("Google error, please try again later!");
      }
    }
  };

  failGoogle = () => {
    toast.error("Google error, please try again later!");
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
                <div className="login-box-inner signup-inner">
                  <div className="display-block logo-box mb-4 text-center">
                    <img
                      src="img/logo-color.png"
                      onClick={this.redirectToLogin}
                    />{" "}
                    <span className="catch">Catch Dj</span>
                  </div>
                  <div className="display-block logo-box mb-4 text-center">
                    <span>Create Your Catch DJ Account</span>
                  </div>
                  <form onSubmit={this.handleSubmit}>
                    <div className="row">
                      <div className="col-xl-6 col-md-6 col-12 col-sm-12">
                        <span
                          className={
                            this.state.nameError
                              ? "input-border fieldError"
                              : "input-border"
                          }
                        >
                          <i className="fa fa-user" aria-hidden="true"></i>
                          <input
                            type="text"
                            placeholder="Name"
                            name="name"
                            value={this.state.name}
                            onChange={this.handleInputChange}
                            autoComplete="off"
                          />
                        </span>
                      </div>

                      <div className="col-xl-6 col-md-6 col-12 col-sm-12">
                        <span
                          className={
                            this.state.emailError
                              ? "input-border fieldError"
                              : "input-border"
                          }
                        >
                          <i className="fa fa-envelope" aria-hidden="true"></i>
                          <input
                            type="text"
                            placeholder="Email"
                            name="email"
                            value={this.state.email}
                            onChange={this.handleInputChange}
                            autoComplete="off"
                          />
                        </span>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-xl-6 col-md-6 col-12 col-sm-12">
                        <span
                          className={
                            this.state.userNameError
                              ? "input-border fieldError"
                              : "input-border"
                          }
                        >
                          <i className="fa fa-user" aria-hidden="true"></i>
                          <input
                            type="text"
                            placeholder="Username"
                            name="userName"
                            value={this.state.userName}
                            onChange={this.handleInputChange}
                            autoComplete="off"
                          />
                        </span>
                      </div>
                      <div className="col-xl-6 col-md-6 col-12 col-sm-12">
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
                            autoComplete="off"
                          />
                        </span>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-xl-6 col-md-6 col-12 col-sm-12">
                        <span
                          className={
                            this.state.birthdayError
                              ? "input-border fieldError"
                              : "input-border"
                          }
                        >
                          <i className="fa fa-calendar" aria-hidden="true"></i>
                          <DatePicker
                            value={this.state.birthday}
                            selected={this.state.birthday}
                            onChange={this.handleChange}
                            maxDate={new Date()}
                            placeholderText="Date of Birth"
                          />
                        </span>
                      </div>

                      <div className="col-xl-6 col-md-6 col-12 col-sm-12 sign-up-radio">
                        <label className="container-radio">
                          Male
                          <input
                            type="radio"
                            checked={
                              this.state.userGender == "male"
                                ? "checked"
                                : false
                            }
                            id="gender0"
                            name="userGender"
                            value={"male"}
                            onChange={this.handleInputChange}
                          />
                          <span className="checkmark-radio"></span>
                        </label>

                        <label className="container-radio female">
                          Female
                          <input
                            type="radio"
                            name="userGender"
                            id="gender1"
                            checked={
                              this.state.userGender == "female"
                                ? "checked"
                                : false
                            }
                            value={"female"}
                            onChange={this.handleInputChange}
                          />
                          <span className="checkmark-radio"></span>
                        </label>
                      </div>
                    </div>
                    <div className="display-block">
                      <div className="row">
                        <div className="col-xl-12 col-sm-12 col-12 login-btn-box text-center">
                          <button type="submit" className="btn login-btn">
                            Create Account
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>
                  <div className="display-block">
                    <div className="row">
                      <div className="col-xl-12 text-center col-sm-12 col-12 register-txt">
                        <a onClick={this.redirectToLogin}>Login</a>
                      </div>
                    </div>
                  </div>

                  <div className="display-block text-center or">
                    <span>or</span>
                  </div>

                  <div className="display-block text-center">
                    {
                      <FacebookLogin
                        appId="2492883237611590"
                        autoLoad={false}
                        fields="name,email,picture"
                        callback={this.handleFBLogin}
                        cssClass="my-facebook-button-class btn btn-fb w-75"
                        icon="fa-facebook"
                      />
                    }

                    {/*<button type="button" className="btn btn-fb">
                    <img src="img/f-icon.png" /> LOGIN WITH FACEBOOK
                  </button>*/}
                  </div>
                  <div className="display-block text-center">
                    <GoogleLogin
                      clientId={
                        "38739005846-2mi6s38na46fb1lbes4ba9b8bko14eqg.apps.googleusercontent.com"
                      }
                      onSuccess={this.responseGoogle}
                      onFailure={this.failGoogle}
                      className="google-btn w-75 btn"
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
  if (state.LoginReducer.action === "REGISTER") {
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
  if (state.LoginReducer.action === "CHECK_EMAIL") {
    if (state.LoginReducer.data == undefined) {
    } else {
      returnState.emailData = state.LoginReducer.data;
    }

    return returnState;
  }

  if (state.LoginReducer.action === "DJ_SOCIAL_REGISTER") {
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
      userSignUpRequest: userSignUpRequest,
      exportEmptyData: exportEmptyData,
      djSocialRegister: djSocialRegister,
      checkEmail: checkEmail,
    },
    dispatch
  );
};

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(Signup));
