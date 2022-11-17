import React, { Component } from "react";
import { withRouter } from "react-router-dom";
import SocialBox from "./SocialBox.js";
import Loader from "react-loader-spinner";

class SuggestedClubs extends Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  handleDetails = (clubId) => {
    return <div>{this.props.history.push(`/user-club/${clubId}`)}</div>;
  };

  render() {
    const suggested = this.props.suggestionsList
      ? this.props.suggestionsList
      : [];
    const liveClubs = this.props.liveClubs ? this.props.liveClubs : [];
    const nearByList = this.props.nearByList ? this.props.nearByList : [];

    return (
      <div className="col-12 col-sm-12 col-lg-4 col-xl-4 left-section mb-5">
        {/*<div className="display-block recent-search-box">
          <div className="row">
            <div className="col-12 col-lg-12 col-sm-12 search-lounge-box">
              <span className="name-lounge">Clubs Near You</span>
            </div>
          </div>
          <div className="row">
            {nearByList &&
              nearByList.map((obj, id) => {
                return (
                  <div
                    className="col-12 col-lg-6 col-sm-12 clubs mb-1 pt-0"
                    key={id}
                  >
                    <span
                      className="recent-clubs"
                      onClick={clubId => {
                        this.handleDetails(obj.club_id);
                      }}
                    >
                      <img src={obj.profile_image} />
                      <span className="name-recent-club">{obj.name}</span>
                    </span>
                  </div>
                );
              })}
          </div>
        </div>*/}

        <div className="display-block recent-search-box pb-4">
          <div className="row">
            <div className="col-12 col-lg-12 col-sm-12 search-lounge-box">
              <span className="name-lounge">Suggested Clubs</span>
              {/*<Loader
                type="Audio"
                color="#fb556b"
                height={100}
                width={100}
                timeout={8000} //3 secs
                visible={suggested.length > 0 ? false : true}
              />*/}
            </div>
          </div>

          <div className="row">
            {suggested &&
              suggested.length > 0 &&
              suggested.map((objx, idx) => {
                return (
                  <div
                    className="col-12 col-lg-6 col-md-6 col-sm-12 clubs mb-1 pt-0 clickMe"
                    key={idx}
                    onClick={(clubId) => {
                      this.handleDetails(objx.club_id);
                    }}
                  >
                    <span className="recent-clubs">
                      <span className="recent-clubs-img">
                        {" "}
                        <img src={objx.profile_image} />
                      </span>

                      {objx.live == false && (
                        <span className="red-color">&nbsp;</span>
                      )}
                      {objx.live && <span className="green-color">&nbsp;</span>}

                      <span className="name-recent-club">{objx.name}</span>
                    </span>
                  </div>
                );
              })}
          </div>
        </div>

        <div className="display-block recent-search-box pb-4 mt-2">
          <div className="row">
            <div className="col-12 col-lg-12 col-sm-12 search-lounge-box">
              <span className="name-lounge">Trending Clubs</span>
            </div>
          </div>
          <div className="row">
            {liveClubs &&
              liveClubs.length > 0 &&
              liveClubs.map((objx, idx) => {
                return (
                  <div
                    className="col-12 col-lg-6 col-md-6 col-sm-12 clubs mb-1 pt-0"
                    key={idx}
                    onClick={(clubId) => {
                      this.handleDetails(objx.club_id);
                    }}
                  >
                    <span className="recent-clubs">
                      <span className="recent-clubs-img">
                        <img src={objx.profile_image} />
                      </span>

                      <span className="green-color">&nbsp;</span>
                      <span className="name-recent-club">{objx.name}</span>
                    </span>
                  </div>
                );
              })}
            {liveClubs && liveClubs.length == 0 && (
              <div className="col-12 col-lg-12 col-sm-12 like-lounge-box text-center">
                {liveClubs.length == 0 ? "No Club is Live Currently" : ""}
              </div>
            )}
          </div>
        </div>
        <SocialBox />
      </div>
    );
  }
}
export default withRouter(SuggestedClubs);
