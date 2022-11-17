import React, { Component } from "react";

export default class RecentSearch extends Component {
  constructor(props) {
    super(props);
  }

  componentDidMount() {}

  render() {
    let suggested = [];
    if (this.props.suggestionsList.length) {
      this.props.suggestionsList.map((obj, id) => {
        if (id < 4) {
          suggested.push(obj);
        }
      });
    }

    return (
      <div className="col-12 col-sm-12 col-lg-4 col-xl-4 left-section ">
        <div className="display-block recent-search-box">
          <div className="row">
            <div className="col-12 col-lg-12 col-sm-12 search-lounge-box">
              <span className="name-lounge">Suggested Clubs</span>
            </div>
          </div>

          <div className="row">
            {this.props.suggestionsList &&
              this.props.suggestionsList.length > 0 &&
              suggested &&
              suggested.map((obj, id) => {
                return (
                  <div
                    className="col-12 col-lg-6 col-sm-12 clubs mb-1 pt-0"
                    key={id}
                  >
                    <span className="recent-clubs">
                      <img src={obj.profile_image} />
                      <span className="name-recent-club">
                        {obj.name ? obj.name : ""}
                      </span>
                    </span>
                  </div>
                );
              })}
          </div>
          <div className="row">
            <div className="col-12 col-lg-12 col-sm-12 add-club-btn">
              <button
                className="btn"
                type="button"
                onClick={this.props.showModal}
              >
                <img src="./img/more.png" /> ADD CLUB
              </button>
            </div>
          </div>
        </div>
      </div>
    );
  }
}
