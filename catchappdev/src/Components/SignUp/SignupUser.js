import React, { Component } from "react";
import validator from "validator";
import { connect } from "react-redux";
import { bindActionCreators } from "redux";
import {
  userRegister,
  exportEmptyData,
  userSocialRegister,
  userCheckEmail,
} from "../../Actions/loginAction.js";
import { withRouter } from "react-router";
import FacebookLogin from "react-facebook-login";
import Geocode from "react-geocode";
import { register } from "../../serviceWorker.js";
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import { toast } from "react-toastify";
import { isFormSubmit } from "../../Utils/services.js";
import LoadingOverlay from "react-loading-overlay";
import ScaleLoader from "react-spinners/ScaleLoader";
import GoogleLogin from "react-google-login";

class SignupUser extends Component {
  constructor(props) {
    super(props);
    localStorage.setItem("user-type", "user");
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
      firstname: "",
      lastname: "",
      userGender: "male",
      birthday: "",
      location: "",
      userName: "",
      userNameError: "",
      errorMessageUserName: "",
      birthdayError: "",
      errorMessageBirthday: "",
      showMe: false,
      fbEmail: "",
      emailFound: "",
    };
  }

  componentDidMount() {
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
            const address = response.results[0].formatted_address;
            // console.log(address);
            const data = address.split(",");
            //console.log(data);
            const city = data[data.length - 3];
            this.setState({ location: city });
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
    let returnState = {};
    props.exportEmptyData();

    if (props.userData !== undefined && props.userData !== state.userData) {
      returnState.userDate = props.userDate;
      returnState.showMe = false;
      returnState.showLoader = false;
      toast.success("Registration Successful!");

      props.history.push("/");
      return returnState;
    }
    if (props.errorData !== undefined && props.errorData !== state.errorData) {
      returnState.errorMessageEmail = props.errorData.message
        ? props.errorData.message
        : "";
      toast.error(returnState.errorMessageEmail);
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
          first_name: state.fbFirstname ? state.fbFirstname : "",
          last_name: state.fbLastname ? state.fbLastname : "",
          email: props.emailData.email ? props.emailData.email : "",
          register_type: "2",
          oauth_key: state.oauthKey ? state.oauthKey : "",
          profile_picture_url: state.fbPictureUrl ? state.fbPictureUrl : "",
          location: state.location ? state.location : "Select Location",
          user_name: props.emailData.email ? props.emailData.email : "",
          //gender: "female",
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
        typeof this.state.firstname == undefined ||
        this.state.firstname == null ||
        this.state.firstname == ""
      ) {
        this.setState({
          //errorMessageName: "Name can not be blank!",
          firstnameError: "fieldError",
        });
        error = true;
      } else {
        error = false;
        this.setState({
          //errorMessageName: "Name can not be blank!",
          firstnameError: "",
        });
      }

      if (
        typeof this.state.email === undefined ||
        this.state.email === null ||
        this.state.email === ""
      ) {
        this.setState({
          // errorMessageEmail: "Email can not be blank!",
          emailError: "fieldError",
        });
        error = true;
      }

      if (
        typeof this.state.userName == undefined ||
        this.state.userName == null ||
        this.state.userName.trim() == ""
      ) {
        this.setState({
          //errorMessageUserName: "Username can not be blank!",
          userNameError: "fieldError",
        });
        error = true;
      }

      if (
        typeof this.state.password === undefined ||
        this.state.password === null ||
        this.state.password === ""
      ) {
        this.setState({
          // errorMessagePass: "Password can not be blank!",
          passError: "fieldError",
        });
        error = true;
      } else {
        this.setState({
          // errorMessagePass: "Password can not be blank!",
          passError: "",
        });
        error = false;
      }

      if (
        typeof this.state.birthday === undefined ||
        this.state.birthday === null ||
        this.state.birthday === ""
      ) {
        this.setState({
          //errorMessageBirthday: "Date of Birth can not be blank!",
          birthdayError: "fieldError",
        });
        error = true;
      } else {
        this.setState({
          //errorMessageBirthday: "Date of Birth can not be blank!",
          birthdayError: "",
        });
        error = false;
      }
      if (!validator.isEmail(this.state.email)) {
        this.setState({
          //errorMessageEmail: "Incorrect email address",
          emailError: "fieldError",
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
          //errorMessageEmail: "Incorrect email address",
          emailError: "fieldError",
        });
        error = true;
        return;
      }
      //======End frontend validation=========

      let formData = {
        first_name: this.state.firstname,
        last_name: this.state.lastname ? this.state.lastname : "",
        email: this.state.email,
        password: this.state.password,
        name: this.state.name,
        user_name: this.state.userName,
        birth_date: this.state.birthday,
        gender: this.state.userGender,
        register_type: "7",
        location: this.state.location,
        device_token: "ABCDEF",
      };
      localStorage.setItem(
        "location",
        JSON.stringify(this.state.location.trim())
      );
      this.setState({ showLoader: true });
      this.props.userRegister(formData);
    }
  };

  redirectToLogin = () => {
    return <div>{this.props.history.push(`/user-login`)}</div>;
  };

  handleChange = (date) => {
    this.setState({
      birthday: date,
    });
  };

  /* handleFbSubmit(e) {
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
      gender: "female",
      web: true,
      device_token: "ABCDEF",
    };
    this.setState({ showLoader: true });
    this.props.userRegister(formData);
  }*/

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
      //gender: "",
      web: true,
      device_token: "ABCDEF",
    };
    this.setState({ showLoader: true });
    this.props.userSocialRegister(formData);
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
          fbGender: "female",
        });
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
        gender: "female",
        device_token: "ABCDEF",
        web: true,
      };
      this.setState({ showLoader: true });
      this.props.userRegister(formData);
    } else {
      toast.error("Facebook error, please try again later!");
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
          location: this.state.location,
          user_name: response.profileObj
            ? response.profileObj.email
            : response.profileObj.userID,
          //gender: "",
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
                <div className="login-box-inner signup-inner">
                  <div className="display-block logo-box mb-4 text-center">
                    <img
                      src="img/logo-color-2.png"
                      onClick={this.redirectToLogin}
                    />{" "}
                    <span className="catch">CatchApp</span>
                  </div>
                  <div className="display-block logo-box mb-4 text-center">
                    <span>Create Your Account on CatchApp</span>
                  </div>
                  <form onSubmit={this.handleSubmit}>
                    <div className="row">
                      <div className="col-xl-6 col-md-6 col-12 col-sm-12">
                        <span
                          className={
                            this.state.firstnameError
                              ? "input-border fieldError"
                              : "input-border"
                          }
                        >
                          <i className="fa fa-user" aria-hidden="true"></i>
                          <input
                            type="text"
                            placeholder="First Name"
                            name="firstname"
                            value={this.state.firstname}
                            onChange={this.handleInputChange}
                            autoComplete="off"
                          />
                        </span>
                      </div>
                      <div className="col-xl-6 col-md-6 col-12 col-sm-12">
                        <span className="input-border">
                          <i className="fa fa-user" aria-hidden="true"></i>
                          <input
                            type="text"
                            placeholder="Last Name"
                            name="lastname"
                            value={this.state.lastname}
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
                    </div>
                    <div className="row">
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
                    </div>
                    <div className="row">
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
                  <div className="row">
                    <div className="col-xl-12 text-center col-sm-12 col-12 register-txt">
                      <a onClick={this.redirectToLogin}>Login</a>
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
  if (state.LoginReducer.action === "USER_REGISTER") {
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

  if (state.LoginReducer.action === "USER_CHECK_EMAIL") {
    if (state.LoginReducer.data == undefined) {
    } else {
      returnState.emailData = state.LoginReducer.data;
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
      userRegister: userRegister,
      exportEmptyData: exportEmptyData,
      userSocialRegister: userSocialRegister,
      userCheckEmail: userCheckEmail,
    },
    dispatch
  );
};

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(withRouter(SignupUser));
