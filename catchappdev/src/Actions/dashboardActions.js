import axios from "axios";
let url = "https://catchapp.live/catchappdevapi/api/";
const dashInstance = axios.create();
//dashInstance.defaults.headers.common["access-token"] = "test007";

dashInstance.interceptors.response.use(
  function (response) {
    if (response.data != undefined) {
    }

    return response;
  },
  function (error) {
    if (!error.response) {
      return { data: { data: "", message: "server_error", status: 500 } };
    } else {
      if (error.response.status == 500) {
        return { data: { data: "", message: "server_error", status: 500 } };
      }
      let msg = error.response.data.message;
      if (
        msg == "invalid_token" ||
        msg == "session_timeout" ||
        msg == "server_error" ||
        msg == "token_not_found"
      ) {
      }

      return Promise.reject(error);
    }
  }
);

export const fetchClubs = (formData) => {
  return (dispatch) => {
    dashInstance
      .post(url + "dj-clubs", formData ? formData : "")
      .then((response) => {
        dispatch({ type: "CLUB_LIST", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "CLUB_LIST", payload: error.response.data });
      });
  };
};

export const fetchProfile = (userId) => {
  return (dispatch) => {
    dashInstance
      .post(url + "dj-details", userId ? userId : "")
      .then((response) => {
        dispatch({ type: "GET_PROFILE", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "GET_PROFILE", payload: error.response.data });
      });
  };
};

export const updateProfile = (formData, data) => {
  return (dispatch) => {
    dashInstance
      .post(url + "update-dj", formData, data)
      .then((response) => {
        dispatch({ type: "UPDATE_PROFILE", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "UPDATE_PROFILE", payload: error.response.data });
      });
  };
};

export const fetchClubDetails = (clubId) => {
  return (dispatch) => {
    dashInstance
      .post(url + "club-details", clubId ? clubId : "")
      .then((response) => {
        dispatch({ type: "GET_CLUBDETAILS", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "GET_CLUBDETAILS", payload: error.response.data });
      });
  };
};

export function exportEmptyData(formData) {
  return (dispatch) => {
    dispatch({
      type: "EMPTY_DATA",
      payload: { data: "", status: 200, message: "" },
    });
  };
}

export const fetchUserClubs = (formData) => {
  return (dispatch) => {
    dashInstance
      .post(url + "home-clubs", formData ? formData : "")
      .then((response) => {
        dispatch({ type: "USER_CLUB_LIST", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "USER_CLUB_LIST", payload: error.response.data });
      });
  };
};

export const searchDj = (formData) => {
  return (dispatch) => {
    dashInstance
      .post(url + "dj-search-clubs", formData ? formData : "")
      .then((response) => {
        dispatch({ type: "SEARCH_CLUBS", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "SEARCH_CLUBS", payload: error.response.data });
      });
  };
};

export const fetchUserProfile = (userId) => {
  return (dispatch) => {
    dashInstance
      .post(url + "user-details", userId ? userId : "")
      .then((response) => {
        dispatch({ type: "GET_USER_PROFILE", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "GET_USER_PROFILE", payload: error.response.data });
      });
  };
};

export const updateUserProfile = (formData) => {
  return (dispatch) => {
    dashInstance
      .post(url + "user-update", formData ? formData : "")
      .then((response) => {
        dispatch({ type: "UPDATE_USER_PROFILE", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "UPDATE_USER_PROFILE", payload: error.response.data });
      });
  };
};

export const userStories = (formData) => {
  return (dispatch) => {
    dashInstance
      .post(url + "user-web-stories", formData ? formData : "")
      .then((response) => {
        dispatch({ type: "USER_STORIES", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "USER_STORIES", payload: error.response.data });
      });
  };
};

export const addClubs = (formData) => {
  return (dispatch) => {
    dashInstance
      .post(url + "send-request-email", formData ? formData : "")
      .then((response) => {
        dispatch({ type: "ADD_CLUB", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "ADD_CLUB", payload: error.response.data });
      });
  };
};

export const liveDJList = () => {
  return (dispatch) => {
    dashInstance
      .get(url + "live-dj-list")
      .then((response) => {
        dispatch({ type: "LIVE_DJ", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "LIVE_DJ", payload: error.response.data });
      });
  };
};

export const topCities = () => {
  return (dispatch) => {
    dashInstance
      .get(url + "top-cities")
      .then((response) => {
        dispatch({ type: "TOP_CITIES", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "TOP_CITIES", payload: error.response.data });
      });
  };
};

export const citySearch = (formData) => {
  return (dispatch) => {
    dashInstance
      .post(url + "search-city", formData ? formData : {})
      .then((response) => {
        dispatch({ type: "SEARCH_CITY", payload: response.data });
      })
      .catch((error) => {
        dispatch({ type: "SEARCH_CITY", payload: error.response.data });
      });
  };
};
