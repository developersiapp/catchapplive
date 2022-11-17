import React, { Component } from "react";

export default class SocialBox extends Component {
  render() {
    return (
      <div className="display-block app-box mt-2">
        <div className="row">
          <div className="col-xl-6 col-sm-6 col-6">
            <a
              href="https://apps.apple.com/us/app/catch-app/id1487117177?ls=1"
              target="_blank"
            >
              <img
                src="./img/apple-store.png"
                alt={"App Download Image"}
                className={"appleButton"}
              />
            </a>
          </div>
          <div className="col-xl-6 col-sm-6 col-6 socialLinks text-center ">
            <a
              href="https://facebook.com/catchapp.live.5"
              target="_blank"
              className="fb-link"
            >
              <i className="fa fa-facebook" aria-hidden="true"></i>
            </a>
            <a
              href="https://twitter.com/catchapp01"
              target="_blank"
              className="tw-link"
            >
              <i className="fa fa-twitter" aria-hidden="true"></i>
            </a>
            <a
              href="https://instagram.com/catchapp.live_"
              target="_blank"
              className="in-link"
            >
              <i className="fa fa-instagram" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>
    );
  }
}
