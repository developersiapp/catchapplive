import React, { Component } from "react";
import Header from "../HeadFoot/Header.js";
import Footer from "../HeadFoot/Footer.js";
import {
  fetchProfile,
  exportEmptyData,
  updateProfile,
  updateUserProfile,
  fetchUserProfile,
} from "../../Actions/dashboardActions.js";
import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import FileUploader from "../FileUploader/FileUploader.js";
import LoadingOverlay from "react-loading-overlay";
import ScaleLoader from "react-spinners/ScaleLoader";
import validator from "validator";
import { toast } from "react-toastify";
import { isFormSubmit } from "../../Utils/services.js";

class Profile extends Component {
  constructor(props) {
    super(props);
    const userData = JSON.parse(localStorage.getItem("userData"));
    const isLoggedIn = JSON.parse(localStorage.getItem("isLoggedIn"));
    const userType = localStorage.getItem("user-type");
    this.state = {
      logged_in: userData ? userData.logged_in : false,
      isLoggedIn: isLoggedIn ? isLoggedIn : 0,
      location: userData ? userData.location : "",
      userName: "",
      profileImage: "",
      loginUserName: "",
      userEmail: "",
      userGender: "",
      updatePassword: false,
      confirmPassword: "",
      userPassword: "",
      djId: userData ? userData.id : "",
      userProfile: {},
      djProfile: {},
      headerName: userData.name ? userData.name : "",
      pictures: [],
      file: "",
      file_name: "",
      defImageCls: "no-display",
      cameraInPreviewCls: "camra-icon dz-clickable no-image",
      uploadedFile: "",
      dzImgObj: {},
      user_image_url: "",
      dzCSS: "",
      dbfirstname: "",
      imagePreviewUrl:
        "https://nwsid.net/wp-content/uploads/2015/05/dummy-profile-pic.png",
      userId: userData ? userData.user_id : "",
      userType: userType ? userType : "",
      firstName: "",
      lastName: "",
      showLoader: false,
      nameError: "",
      errorMessageName: "",
      userNameError: "",
      passwordError: "",
      inShow: false,
      profileUpdate: false,
      authKey: userData ? userData.oauth_key : "",
      pictureUrl: "",
      /*userHeadName: userData.first_name
        ? userData.first_name + " " + userData.last_name
        : "",*/
      userFirstName: userData ? userData.first_name : "",
      userLastName: userData ? userData.last_name : "",
    };
  }
  componentDidMount() {
    if (this.state.userType == "") {
      window.location.href = `/`;
    }

    let djId = {
      id: this.state.djId,
    };
    let userId = {
      user_id: this.state.userId,
    };
    if (
      this.state.logged_in == true ||
      this.state.isLoggedIn == 1 ||
      this.state.authKey !== ""
    ) {
      this.setState({ showLoader: true });
      if (this.state.userType == "dj") {
        this.props.fetchProfile(djId);
      }
      if (this.state.userType == "user") {
        this.props.fetchUserProfile(userId);
      }
    }
  }

  handleInputChange = (event) => {
    this.props.exportEmptyData();
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
      case "file": {
        value = target.files[0];
        break;
      }
    }
    this.setState({ [event.target.name]: value, userChanged: true });
  };

  static getDerivedStateFromProps(props, state) {
    let returnState = {};
    props.exportEmptyData();
    if (
      props.djProfile !== undefined &&
      props.djProfile !== state.djProfile &&
      props.djDate !== state.djDate
    ) {
      returnState.djDate = props.djDate;
      returnState.djProfile = props.djProfile ? props.djProfile.data : {};
      returnState.location = props.djProfile.data.location
        ? props.djProfile.data.location
        : "";
      returnState.userName = props.djProfile ? props.djProfile.data.name : "";

      returnState.profileImage = props.djProfile
        ? props.djProfile.data.profile_image
        : "https://nwsid.net/wp-content/uploads/2015/05/dummy-profile-pic.png";

      returnState.imagePreviewUrl = props.djProfile.data.profile_image
        ? props.djProfile.data.profile_image
        : props.djProfile.data.profile_picture_url;

      if (
        returnState.imagePreviewUrl == undefined ||
        returnState.imagePreviewUrl == null ||
        returnState.imagePreviewUrl.trim() == ""
      ) {
        returnState.imagePreviewUrl =
          "https://nwsid.net/wp-content/uploads/2015/05/dummy-profile-pic.png";
      }
      returnState.loginUserName = props.djProfile
        ? props.djProfile.data.user_name
        : "";
      returnState.userEmail = props.djProfile ? props.djProfile.data.email : "";
      returnState.userGender = props.djProfile
        ? props.djProfile.data.gender
        : "";

      returnState.headerName = props.djProfile ? props.djProfile.data.name : "";
      returnState.updatePassword = false;
      returnState.userPassword = "";
      returnState.confirmPassword = "";

      returnState.showLoader = false;
      if (
        props.update !== undefined &&
        props.update == true &&
        state.inShow == true
      ) {
        returnState.update = false;
        returnState.inShow = false;
        toast.success("Profile picture updated!");
      } else if (
        (returnState.update =
          true && state.inShow == false && state.profileUpdate == true)
      ) {
        returnState.update = false;
        returnState.profileUpdate = false;
        toast.success("Profile updated successully");
        returnState.nameError = "";
        returnState.userNameError = "";
        returnState.passwordError = "";
        returnState.confirmPasswordError = "";
        returnState.genderError = "";
      }

      let userData = JSON.parse(localStorage.getItem("userData"));
      userData.profile_image = props.djProfile
        ? props.djProfile.data.profile_image
        : "https://nwsid.net/wp-content/uploads/2015/05/dummy-profile-pic.png";
      localStorage.setItem("userData", JSON.stringify(userData));
      return returnState;
    }

    if (
      props.userProfile !== undefined &&
      props.userProfile !== state.userProfile &&
      props.userDate !== state.userDate
    ) {
      returnState.userDate = props.userDate;
      returnState.userProfile = props.userProfile ? props.userProfile.data : {};
      returnState.location = props.userProfile.data.location
        ? props.userProfile.data.location
        : "";
      returnState.firstName = props.userProfile
        ? props.userProfile.data.first_name
        : "";

      returnState.lastName = props.userProfile
        ? props.userProfile.data.last_name
        : "";

      returnState.profileImage = props.userProfile
        ? props.userProfile.data.profile_image
        : "https://nwsid.net/wp-content/uploads/2015/05/dummy-profile-pic.png";

      returnState.imagePreviewUrl = props.userProfile.data.profile_image
        ? props.userProfile.data.profile_image
        : props.userProfile.data.profile_picture_url;

      if (
        returnState.imagePreviewUrl == undefined ||
        returnState.imagePreviewUrl == null ||
        returnState.imagePreviewUrl.trim() == ""
      ) {
        returnState.imagePreviewUrl =
          "https://nwsid.net/wp-content/uploads/2015/05/dummy-profile-pic.png";
      }

      returnState.updatePassword = false;
      returnState.userPassword = "";
      returnState.confirmPassword = "";
      returnState.loginUserName = props.userProfile
        ? props.userProfile.data.user_name
        : "";
      returnState.userEmail = props.userProfile
        ? props.userProfile.data.email
        : "";
      returnState.userGender = props.userProfile
        ? props.userProfile.data.gender
        : "";
      returnState.headerName = props.userProfile
        ? props.userProfile.data.name
        : "";

      /* returnState.userHeadName = props.userProfile
        ? props.userProfile.data.first_name +
          " " +
          props.userProfile.data.last_name
        : "";*/

      returnState.userFirstName = props.userProfile.data
        ? props.userProfile.data.first_name
        : "";

      returnState.userLastName = props.userProfile.data
        ? props.userProfile.data.last_name
        : "";

      returnState.showLoader = false;

      if (
        props.update !== undefined &&
        props.update == true &&
        state.inShow == true
      ) {
        returnState.update = false;
        returnState.inShow = false;
        toast.success("Profile picture updated!");
      } else if (
        (returnState.update =
          true && state.inShow == false && state.profileUpdate == true)
      ) {
        returnState.update = false;
        returnState.profileUpdate = false;
        toast.success("Profile updated successully");
        returnState.firstNameError = "";
        returnState.userNameError = "";
        returnState.passwordError = "";
        returnState.confirmPasswordError = "";
        returnState.lastNameError = "";
        returnState.genderError = "";
      }
      let userData = JSON.parse(localStorage.getItem("userData"));
      userData.profile_image = props.userProfile
        ? props.userProfile.data.profile_image
        : "https://nwsid.net/wp-content/uploads/2015/05/dummy-profile-pic.png";
      localStorage.setItem("userData", JSON.stringify(userData));
      return returnState;
    }
    if (
      props.updateError !== undefined &&
      props.updateError.error == true &&
      props.updateError !== state.updateError
    ) {
      returnState.showLoader = false;
      returnState.userNameError = "fieldError";
      //returnState.loginUserName = "fieldError";
      toast.error(props.updateError.message);
      return returnState;
    }
    return null;
  }

  uploadPhoto = () => {};

  handleSubmit = (event) => {
    event.preventDefault();
    this.props.exportEmptyData();
    if (isFormSubmit()) {
      let userData = JSON.parse(localStorage.getItem("userData"));

      let error = false;
      if (this.state.userType !== "dj") {
        if (
          this.state.firstName == undefined ||
          this.state.firstName == null ||
          this.state.firstName.trim() == ""
        ) {
          this.setState({
            firstNameError: "fieldError",
          });
          error = true;
        }
        if (
          this.state.lastName == undefined ||
          this.state.lastName == null ||
          this.state.lastName.trim() == ""
        ) {
          /*this.setState({
            lastNameError: "fieldError",
          });
          error = true;*/
        }

        if (
          this.state.userGender == undefined ||
          this.state.userGender == null ||
          this.state.userGender.trim() == ""
        ) {
          this.setState({
            genderError: "fieldError",
          });
          error = true;
          //toast.error("Gender required!");
        }

        if (
          this.state.loginUserName == undefined ||
          this.state.loginUserName == null ||
          this.state.loginUserName.trim() == ""
        ) {
          this.setState({
            userNameError: "fieldError",
          });
          error = true;
        }
      } else {
        if (
          this.state.userName == undefined ||
          this.state.userName == null ||
          this.state.userName.trim() == ""
        ) {
          this.setState({
            nameError: "fieldError",
          });
          error = true;
        }
        if (
          this.state.loginUserName == undefined ||
          this.state.loginUserName == null ||
          this.state.loginUserName.trim() == ""
        ) {
          this.setState({
            userNameError: "fieldError",
          });
          error = true;
        }
        if (
          this.state.userGender == undefined ||
          this.state.userGender == null ||
          this.state.userGender.trim() == ""
        ) {
          this.setState({
            genderError: "fieldError",
          });
          error = true;
          //toast.error("Gender required!");
        }
      }

      if (this.state.updatePassword) {
        if (
          this.state.userPassword == undefined ||
          this.state.userPassword == null ||
          this.state.userPassword == "" ||
          this.state.userPassword.trim() == ""
        ) {
          this.setState({
            passwordError: "fieldError",
          });
          error = true;
        }

        if (
          this.state.confirmPassword == undefined ||
          this.state.confirmPassword == null ||
          this.state.confirmPassword == "" ||
          this.state.confirmPassword.trim() == ""
        ) {
          this.setState({
            confirmPasswordError: "fieldError",
          });
          error = true;
        }

        if (this.state.userPassword !== this.state.confirmPassword) {
          error = true;
          toast.error("Password & Confirm Password not matched");
          this.setState({
            confirmPasswordError: "fieldError",
            passwordError: "fieldError",
          });
          return;
        }

        if (this.state.userPassword && this.state.userPassword.length < 8) {
          toast.error("Passwords must be at least 8 characters");
          error = true;
          this.setState({
            //errorMessagePass: "Password can not be blank!",
            passwordError: "fieldError",
          });
          return;
        }
      }

      if (this.state.loginUserName && this.state.loginUserName.length < 6) {
        error = true;
        toast.error("Username must be at least 6 characters");
        this.setState({
          userNameError: "fieldError",
        });
        return;
      }
      //console.log(error);
      if (error === true) {
        return;
      }
      let formData = {};
      this.setState({ showLoader: true });
      if (this.state.userType == "user") {
        formData = {
          user_id: this.state.userId,
          first_name: this.state.firstName,
          last_name: this.state.lastName,
          user_name: this.state.loginUserName,
          password: this.state.userPassword,
          location: this.state.location,
          gender: this.state.userGender,
          picture_url: this.state.imagePreviewUrl,
          email: this.state.userEmail,
        };
        userData.first_name = this.state.firstName;
        userData.last_name = this.state.lastName ? this.state.lastName : "";
        localStorage.setItem("userData", JSON.stringify(userData));
        this.setState({ profileUpdate: true });
        this.props.updateUserProfile(formData);
      } else {
        formData = {
          id: this.state.djId,

          name: this.state.userName,
          user_name: this.state.loginUserName,
          password: this.state.userPassword,
          location: this.state.location,
          gender: this.state.userGender,
          picture_url: this.state.imagePreviewUrl,
          email: this.state.userEmail,
        };
        userData.name = this.state.userName;
        localStorage.setItem("userData", JSON.stringify(userData));
        this.setState({ profileUpdate: true });
        this.props.updateProfile(formData);
      }
    }
  };

  _handleImageChange(e) {
    e.preventDefault();
    let file = e.target.files[0];
    this.setState({ file: file });

    let reader = new FileReader();
    reader.onloadend = () => {
      var binaryData = reader.result; // Encoded Base 64 File String

      var data = reader.result.split(",")[1];
      var binaryBlob = atob(data);

      //console.log("Encoded Binary File String:", JSON.stringify(binaryBlob));

      this.setState({ profileImage: JSON.stringify(binaryBlob) });

      //var base64String = window.btoa(binaryData);
      //console.log(base64String);
      this.setState({
        imagePreviewUrl: binaryData,
        inShow: true,
      });
    };

    if (file) {
      reader.readAsDataURL(file);
    }

    if (file) {
      let form_data = new FormData();
      form_data.append("profile_image", file);

      if (this.state.userType == "user") {
        form_data.append("last_name", this.state.lastName);
      }
      if (this.state.userType == "dj") {
        form_data.append("id", this.state.djId);
        if (form_data) {
          this.setState({ showLoader: true });
          this.props.updateProfile(form_data);
        }
      }
      if (this.state.userType == "user") {
        form_data.append("user_id", this.state.userId);
        this.setState({ showLoader: true });
        this.props.updateUserProfile(form_data);
      }
    }
  }

  render() {
    let { imagePreviewUrl } = this.state;
    let $imagePreview = null;
    if (imagePreviewUrl) {
      $imagePreview = <img src={imagePreviewUrl} />;
    } else {
      $imagePreview = <div className="previewText"></div>;
    }
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
                userProfile={this.state.imagePreviewUrl}
                userName={
                  this.state.headerName ? this.state.headerName : userName
                }
              />
            </div>
          </div>
          <div className="container profile-container">
            <form onSubmit={this.handleSubmit}>
              <section className="profile-page">
                <div className="row mb-5 profile-img">
                  <div className="col-sm-8 mx-auto text-center">
                    <div className="img-box">
                      <div className="previewComponent">
                        <div className="imgPreview">
                          {this.state.inShow ? (
                            $imagePreview
                          ) : (
                            <img
                              className="inshow"
                              src={this.state.imagePreviewUrl}
                            />
                          )}
                          <label className="custom-file-upload">
                            <input
                              type="file"
                              onChange={(e) => this._handleImageChange(e)}
                              accept="image/png, image/jpeg, image/jpg"
                            />
                            <span className="edit-bg">
                              <i className="fa fa-pencil"></i>
                            </span>
                          </label>
                        </div>
                      </div>
                    </div>
                    {/*<div className="edit-icon">
                      <span className="edit-bg">
                        <img src="img/pencil.png" />
                      </span>
                    </div>*/}
                  </div>
                </div>

                <div className="row">
                  <div className="col-sm-8 mx-auto">
                    <form>
                      <div className="row">
                        {this.state.userType == "dj" && (
                          <div className="form-group col-sm-12 col-md-12 col-lg-6">
                            <label htmlFor="inputEmail4">Name</label>
                            <input
                              type="text"
                              name="userName"
                              className={
                                this.state.nameError
                                  ? "form-control fieldError"
                                  : "form-control"
                              }
                              value={this.state.userName}
                              onChange={this.handleInputChange}
                              autoComplete="off"
                            />
                          </div>
                        )}

                        {this.state.userType == "user" && (
                          <div className="form-group col-sm-12 col-md-12 col-lg-6">
                            <label htmlFor="inputEmail4">First Name</label>
                            <input
                              type="text"
                              name="firstName"
                              className={
                                this.state.firstNameError
                                  ? "form-control fieldError"
                                  : "form-control"
                              }
                              value={this.state.firstName}
                              onChange={this.handleInputChange}
                              autoComplete="off"
                            />
                          </div>
                        )}

                        {this.state.userType == "user" && (
                          <div className="form-group col-sm-12 col-md-12 col-lg-6">
                            <label htmlFor="inputEmail4">Last Name</label>
                            <input
                              type="text"
                              name="lastName"
                              className={
                                this.state.lastNameError
                                  ? "form-control fieldError"
                                  : "form-control"
                              }
                              value={this.state.lastName}
                              onChange={this.handleInputChange}
                              autoComplete="off"
                            />
                          </div>
                        )}
                        <div className="form-group col-sm-12 col-md-12 col-lg-6">
                          <label htmlFor="inputPassword4">Username</label>
                          <input
                            type="text"
                            className={
                              this.state.userNameError
                                ? "form-control fieldError"
                                : "form-control"
                            }
                            name="loginUserName"
                            value={this.state.loginUserName}
                            onChange={this.handleInputChange}
                            autoComplete="off"
                          />
                        </div>

                        <div className="form-group col-sm-12 col-md-12 col-lg-6">
                          <label htmlFor="inputEmail4">Email Address</label>
                          <input
                            type="email"
                            name="userEmail"
                            value={this.state.userEmail}
                            className="form-control"
                            autoComplete="off"
                            readOnly
                          />
                        </div>
                        <div className="form-group col-sm-12 col-md-12 col-lg-6 gender-container">
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
                            <span
                              className={
                                this.state.genderError
                                  ? "checkmark-radio radio-error"
                                  : "checkmark-radio"
                              }
                            ></span>
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
                            <span
                              className={
                                this.state.genderError
                                  ? "checkmark-radio radio-error"
                                  : "checkmark-radio"
                              }
                            ></span>
                          </label>
                        </div>
                      </div>

                      <div className="row">
                        <div className="form-group col-sm-12 col-md-12 col-lg-6 update-group">
                          <label className="container-checkbox">
                            <span className="rm-text">Update Password</span>
                            <input
                              type="checkbox"
                              name="updatePassword"
                              checked={
                                this.state.updatePassword ? "checked" : false
                              }
                              onChange={this.handleInputChange}
                            />
                            <span className="checkmark"></span>
                          </label>
                        </div>
                      </div>

                      {this.state.updatePassword && (
                        <div className="row">
                          <div className="form-group col-sm-12 col-md-12 col-lg-6">
                            <label htmlFor="inputEmail4">Password</label>
                            <input
                              type="password"
                              className={
                                this.state.passwordError
                                  ? "form-control fieldError"
                                  : "form-control"
                              }
                              name="userPassword"
                              value={this.state.userPassword}
                              onChange={this.handleInputChange}
                              autoComplete="off"
                            />
                          </div>
                          <div className="form-group col-sm-12 col-md-12 col-lg-6">
                            <label htmlFor="inputPassword4">
                              Confirm Password
                            </label>
                            <input
                              type="password"
                              name="confirmPassword"
                              className={
                                this.state.confirmPasswordError
                                  ? "form-control fieldError"
                                  : "form-control"
                              }
                              value={this.state.confirmPassword}
                              onChange={this.handleInputChange}
                              autoComplete="off"
                            />
                          </div>
                        </div>
                      )}
                      <div className="row">
                        <div className="col-xl-12 col-sm-12 col-12 save-btn-box text-center mt-4 mb-5">
                          <button type="submit" className="btn login-btn">
                            Save Changes
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </section>
            </form>
          </div>
          <Footer />
        </LoadingOverlay>
      </div>
    );
  }
}
function mapStateToProps(state) {
  const returnState = {};
  if (state.DashboardReducer.action === "GET_PROFILE") {
    if (state.DashboardReducer.data.error == true) {
    } else {
      returnState.djProfile = state.DashboardReducer.data;
      returnState.djDate = new Date();
    }
  }

  if (state.DashboardReducer.action === "UPDATE_PROFILE") {
    if (state.DashboardReducer.data.error == true) {
      returnState.updateError = state.DashboardReducer.data;
    } else {
      returnState.djProfile = state.DashboardReducer.data;
      returnState.update = true;
      returnState.djDate = new Date();
    }
  }

  if (state.DashboardReducer.action === "GET_USER_PROFILE") {
    if (state.DashboardReducer.data.error == true) {
    } else {
      returnState.userProfile = state.DashboardReducer.data;
      returnState.userDate = new Date();
    }
  }

  if (state.DashboardReducer.action === "UPDATE_USER_PROFILE") {
    if (state.DashboardReducer.data.error == true) {
      returnState.updateError = state.DashboardReducer.data;
    } else {
      returnState.userProfile = state.DashboardReducer.data;

      returnState.update = true;
      returnState.userDate = new Date();
    }
  }

  return returnState;
}

function mapDispatchToProps(dispatch) {
  return bindActionCreators(
    {
      fetchProfile: fetchProfile,
      exportEmptyData: exportEmptyData,
      updateProfile: updateProfile,
      fetchUserProfile: fetchUserProfile,
      updateUserProfile: updateUserProfile,
    },
    dispatch
  );
}

export default connect(mapStateToProps, mapDispatchToProps)(Profile);
