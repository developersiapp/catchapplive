import axios from "axios";
let url = "https://catchapp.live/catchappdevapi/api/";
const streamInstance = axios.create();

streamInstance.interceptors.response.use(
  function(response) {
    if (response.data != undefined) {
    }

    return response;
  },
  function(error) {
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

export const createStream = formData => {
  return dispatch => {
    streamInstance
      .post(url + "create-live-stream", formData ? formData : "")
      .then(response => {
        dispatch({ type: "CREATE_STREAM", payload: response.data });
      })
      .catch(error => {
        dispatch({ type: "CREATE_STREAM", payload: error.response.data });
      });
  };
};

export const startStream = formData => {
  return dispatch => {
    streamInstance
      .put(url + "start-stream", formData ? formData : "")
      .then(response => {
        dispatch({ type: "START_STREAM", payload: response.data });
      })
      .catch(error => {
        dispatch({ type: "START_STREAM", payload: error.response.data });
      });
  };
};

export const checkStreamState = formData => {
  return dispatch => {
    streamInstance
      .post(url + "fetch-lstream-state", formData ? formData : "")
      .then(response => {
        dispatch({ type: "STREAM_STATE", payload: response.data });
      })
      .catch(error => {
        dispatch({ type: "STREAM_STATE", payload: error.response.data });
      });
  };
};

export const stopStream = formData => {
  return dispatch => {
    streamInstance
      .put(url + "stop-stream", formData ? formData : "")
      .then(response => {
        dispatch({ type: "STOP_STREAM", payload: response.data });
      })
      .catch(error => {
        dispatch({ type: "STOP_STREAM", payload: error.response.data });
      });
  };
};

export const setStreamDetails = formData => {
  return dispatch => {
    streamInstance
      .post(url + "log-stream", formData ? formData : "")
      .then(response => {
        dispatch({ type: "SET_STREAM_DETAILS", payload: response.data });
      })
      .catch(error => {
        dispatch({ type: "SET_STREAM_DETAILS", payload: error.response.data });
      });
  };
};
