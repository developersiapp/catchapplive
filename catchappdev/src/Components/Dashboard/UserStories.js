import React, { Component } from "react";
import { userStories } from "../../Actions/dashboardActions.js";
import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import Stories from "react-insta-stories";
import Marquee from "react-double-marquee";
import Loader from "react-loader-spinner";

const initStories = () => {
  return {
    url: "",
    header: {
      heading: "",
      subheading: "",
      profileImage: "",
      userStories: "",
    },
  };
};

class UserStories extends Component {
  constructor(props) {
    super(props);
    this.state = {
      allStories: [],
      userStoriesData: {},

      stories: [initStories()],
    };
  }

  componentDidMount() {
    let userId = this.props.userId;
    let formData = {
      id: userId,
    };
    this.props.userStories(formData);
  }

  static getDerivedStateFromProps(props, state) {
    let returnState = {};

    if (
      props.userStoriesData != undefined &&
      props.userStoriesData.success == true &&
      props.userStoriesData != state.userStoriesData
    ) {
      let storiesData = [];
      returnState.userStoriesData = props.userStoriesData
        ? props.userStoriesData
        : {};
      if (props.userStoriesData) {
        props.userStoriesData.data.map((obj, id) => {
          if (obj.stories && obj.stories.length) {
            storiesData.push(obj);
          }
        });
      }

      returnState.allStories = props.userStoriesData ? storiesData : [];

      return returnState;
    }
    return null;
  }

  stories = (storyId) => {
    let id = storyId;
    const stories = this.state.allStories;

    let userStories = [];
    //console.log(stories);
    stories.map((obj, idx) => {
      if (idx == storyId) {
        obj.stories.map((objx, id) => {
          userStories.push(objx);
        });
      }
    });
    this.setState({ userStories: userStories });

    /*   return (
      <Stories
        stories={userStories}
        defaultInterval={1500}
        width={432}
        height={768}
      />
    );*/
  };

  hideUserStory = () => {
    this.setState({ userStories: [] });
  };

  dismissModal = () => {
    this.setState({ userStories: [] });
  };
  render() {
    let allStories = this.state.allStories;

    /* const stories = [
      {
        url: "https://picsum.photos/1080/1920",

        header: {
          heading: "Ravneet Singh",
          subheading: "Posted 5h ago",
          profileImage: "https://picsum.photos/1000/1000"
        },
        url: "https://picsum.photos/1080/1920",

        header: {
          heading: "Ravneet Singh",
          subheading: "Posted 5h ago",
          profileImage: "https://picsum.photos/1000/1000"
        }
      }
    ];*/

    return (
      <div>
        <section className="bg-search">
          <div className="row stories">
            <div className="col-xl-12 col-12">
              <h5>Stories</h5>
              {/*<Loader
                type="Audio"
                color="#fb556b"
                height={100}
                width={100}
                timeout={8000} //3 secs
                visible={allStories.length > 0 ? false : true}
              />*/}
            </div>
          </div>
          <div className="row stories-row">
            <div className="col-xl-12 col-12">
              <ul className="story-ul">
                <Marquee delay={3000} speed={0.02} direction={"left"}>
                  {/* <li className="story-col">
                  <img src="img/ur-story1.png" />
                  <span className="story-heading">Your Story</span>
                  <span className="story-btn">
                    <img src="img/plus-icon.png" />
                  </span>
                </li>*/}
                  {allStories &&
                    allStories.map((obj, id) => {
                      return (
                        <li
                          className="story-col"
                          onClick={this.stories.bind(this, id)}
                          key={id}
                        >
                          <img
                            src={
                              obj.profile_picture
                                ? obj.profile_picture
                                : "https://nwsid.net/wp-content/uploads/2015/05/dummy-profile-pic.png"
                            }
                          />
                          <span className="story-heading">{obj.name}</span>
                        </li>
                      );
                    })}
                </Marquee>
              </ul>
            </div>
          </div>
        </section>

        {this.state.userStories && this.state.userStories.length > 0 && (
          <div
            className="modal fade"
            id="addclub"
            tabIndex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
          >
            <div className="modal-dialog" role="document">
              <div className="modal-content story-modal">
                <button
                  type="button"
                  className="close text-right"
                  data-dismiss="modal"
                  aria-label="Close"
                  onClick={() => this.dismissModal()}
                >
                  <span aria-hidden="true">
                    <img src="img/close2.png" />
                  </span>
                </button>
                <div className="modal-body ">
                  <div className="display-block type-something">
                    {this.state.userStories.length > 0 && (
                      <Stories
                        stories={this.state.userStories}
                        width={"100%"}
                        height={530}
                        onAllStoriesEnd={this.hideUserStory}
                      />
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    );
  }
}
function mapStateToProps(state) {
  const returnState = {};
  if (state.DashboardReducer.action === "USER_STORIES") {
    if (state.DashboardReducer.data.error == true) {
    } else {
      returnState.userStoriesData = state.DashboardReducer.data;
    }
  }
  return returnState;
}

function mapDispatchToProps(dispatch) {
  return bindActionCreators(
    {
      userStories: userStories,
    },
    dispatch
  );
}

export default connect(mapStateToProps, mapDispatchToProps)(UserStories);
