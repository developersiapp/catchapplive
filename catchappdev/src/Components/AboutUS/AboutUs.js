import React, { Component } from "react";
import Header from "../HeadFoot/Header.js";

import Footer from "../HeadFoot/Footer.js";

export default class AboutUs extends Component {
  constructor(props) {
    const userData = JSON.parse(localStorage.getItem("userData"));
    const isLoggedIn = JSON.parse(localStorage.getItem("isLoggedIn"));
    const userType = localStorage.getItem("user-type");
    super(props);

    this.state = {
      logged_in: userData ? userData.logged_in : false,
      isLoggedIn: isLoggedIn ? isLoggedIn : 0,
      headerName: userData ? userData.name : "",
      userHeadName: userData
        ? userData.first_name +
          " " +
          (userData.last_name ? userData.last_name : "")
        : "",
      imagePreviewUrl: userData
        ? userData.profile_image
        : userData
        ? userData.profile_picture_url
        : "",
    };
  }
  render() {
    return (
      <div className="">
        <div className="container-fluid menu">
          <div className="container">
            <Header
              userProfile={this.state.imagePreviewUrl}
              userName={
                this.state.headerName
                  ? this.state.headerName
                  : this.state.userHeadName
              }
            />
          </div>
        </div>
        <div className="container">
          <div className="content-bg">
            <div className="row">
              <div className="col-12">
                <h1 className="inner-page-heading">About Us</h1>
              </div>
              <div className="col-12 inner-content">
                <p>
                  CatchApp is a live streaming platform for nightlife industry.
                  We connect online audiences with nightlife experience in
                  real-time Users can be able to listen and know what's
                  happening in clubs/venues before there go out. Nightlife
                  industry businesses can be able to attract online audiences
                  through live streaming music on CatchApp and real-time updates
                  on specials to their target market. Through CatchApp stories
                  and insights updates (traffic and gender) users can get hype
                  to have fun either virtually or physically. Dj are able to
                  attract real-time fan-base into their venues and also virtual
                  audiences streaming around the world.
                </p>

                <p className="address-section">
                  <strong>At CatchApp Every Moment Counts</strong>
                  <br />
                  <i class="fa fa-building" aria-hidden="true"></i>{" "}
                  <b>Address</b>: CatchApp,HQ Cleveland, Ohio U.S.A
                  <br />
                  <i class="fa fa-phone" aria-hidden="true"></i>{" "}
                  <b>Contact Tel</b>:{" "}
                  <a href="tel:+12163339862">+12163339862</a>
                  <br />
                  <i class="fa fa-envelope" aria-hidden="true"></i> <b>Email</b>
                  :{" "}
                  <a href="mailto:catchapplive@gmail.com">
                    catchapplive@gmail.com
                  </a>
                  <br />
                  {/*<i class="fa fa-globe" aria-hidden="true"></i> <b>Website</b>:{" "}
                  <a href="www.catchapp.live" target="_blank">
                    www.catchapp.live
                  </a>*/}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div className={"container"}>
          {" "}
          <div className="abouts-footer100vh">
            <div className="row">
              <div className="col-12 copyright text-center">
                Copyright © 2020 <span className="color-footer">CatchApp</span>{" "}
                | All Rights Reserved{" "}
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }
}
