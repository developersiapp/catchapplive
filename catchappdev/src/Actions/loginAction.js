import axios from "axios";
let url = "https://catchapp.live/catchappdevapi/api/";
const signinInstance = axios.create();

//=======Login action==========
signinInstance.interceptors.response.use(
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

export function djSignInRequest(userData) {
  return (dispatch) => {
    signinInstance
      .post(url + "dj-login", userData)
      .then((response) => {
        dispatch({ type: "LOGIN_SUCCESSFULL", payload: response.data });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({ type: "LOGIN_ERROR", payload: error.response.data });
        }
      });
  };
}

export function userSignInRequest(userData) {
  return (dispatch) => {
    signinInstance
      .post(url + "user-login", userData)
      .then((response) => {
        dispatch({ type: "USER_LOGIN_SUCCESSFULL", payload: response.data });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({ type: "USER_LOGIN_ERROR", payload: error.response.data });
        }
      });
  };
}

export function socialLogin(formData) {
  return (dispatch) => {
    signinInstance
      .post(url + "social-dj-login", formData)
      .then((response) => {
        dispatch({ type: "SOCIAL_LOGIN_SUCCESSFULL", payload: response.data });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({
            type: "SOCIAL_LOGIN_ERROR",
            payload: error.response.data,
          });
        }
      });
  };
}

export function forgetPassword(formData) {
  return (dispatch) => {
    signinInstance
      .post(url + "dj-reset-password", formData)
      .then((response) => {
        dispatch({
          type: "FORGET_PASSWORD_SUCCESSFULL",
          payload: response.data,
        });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({
            type: "FORGET_PASSWORD_ERROR",
            payload: error.response.data,
          });
        }
      });
  };
}

export function resPassword(formData) {
  return (dispatch) => {
    signinInstance
      .post(url + "update-dj-password", formData)
      .then((response) => {
        dispatch({
          type: "RESET_PASSWORD_SUCCESSFULL",
          payload: response.data,
        });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({
            type: "RESET_PASSWORD_ERROR",
            payload: error.response.data,
          });
        }
      });
  };
}

export function userSignUpRequest(userData) {
  return (dispatch) => {
    signinInstance
      .post(url + "dj-register", userData)
      .then((response) => {
        dispatch({ type: "REGISTER_SUCCESSFULL", payload: response.data });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({
            type: "REGISTER_ERROR",
            payload: error.response.data,
          });
        }
      });
  };
}

export function userRegister(userData) {
  return (dispatch) => {
    signinInstance
      .post(url + "user-register", userData)
      .then((response) => {
        dispatch({ type: "USER_REGISTER_SUCCESSFULL", payload: response.data });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({
            type: "USER_REGISTER_ERROR",
            payload: error.response.data,
          });
        }
      });
  };
}

export function userForgetPassword(formData) {
  return (dispatch) => {
    signinInstance
      .post(url + "user-forgot-password", formData)
      .then((response) => {
        dispatch({
          type: "USER_FORGET_PASSWORD_SUCCESSFULL",
          payload: response.data,
        });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({
            type: "USER_FORGET_PASSWORD_ERROR",
            payload: error.response.data,
          });
        }
      });
  };
}

export function userResPassword(formData) {
  return (dispatch) => {
    signinInstance
      .post(url + "update-user-password", formData)
      .then((response) => {
        dispatch({
          type: "USER_RESET_PASSWORD_SUCCESSFULL",
          payload: response.data,
        });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({
            type: "USER_RESET_PASSWORD_ERROR",
            payload: error.response.data,
          });
        }
      });
  };
}

export function userSocialLogin(formData) {
  return (dispatch) => {
    signinInstance
      .post(url + "social-user-login", formData)
      .then((response) => {
        dispatch({
          type: "USER_SOCIAL_LOGIN_SUCCESSFULL",
          payload: response.data,
        });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({
            type: "USER_SOCIAL_LOGIN_ERROR",
            payload: error.response.data,
          });
        }
      });
  };
}

export function checkEmail(formData) {
  return (dispatch) => {
    signinInstance
      .post(url + "dj-check-email", formData)
      .then((response) => {
        dispatch({
          type: "CHECK_EMAIL",
          payload: response.data,
        });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({
            type: "CHECK_EMAIL",
            payload: error.response.data,
          });
        }
      });
  };
}

export function userCheckEmail(formData) {
  return (dispatch) => {
    signinInstance
      .post(url + "user-check-email", formData)
      .then((response) => {
        dispatch({
          type: "USER_CHECK_EMAIL",
          payload: response.data,
        });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({
            type: "USER_CHECK_EMAIL",
            payload: error.response.data,
          });
        }
      });
  };
}

export function djSocialRegister(userData) {
  return (dispatch) => {
    signinInstance
      .post(url + "dj-social-web", userData)
      .then((response) => {
        dispatch({
          type: "DJ_SOCIAL_REGISTER_SUCCESSFULL",
          payload: response.data,
        });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({
            type: "DJ_SOCIAL_REGISTER_ERROR",
            payload: error.response.data,
          });
        }
      });
  };
}

export function userSocialRegister(userData) {
  return (dispatch) => {
    signinInstance
      .post(url + "user-social-web", userData)
      .then((response) => {
        dispatch({
          type: "USER_SOCIAL_REGISTER_SUCCESSFULL",
          payload: response.data,
        });
      })
      .catch((error) => {
        if (error.response) {
          dispatch({
            type: "USER_SOCIAL_REGISTER_ERROR",
            payload: error.response.data,
          });
        }
      });
  };
}

export function exportEmptyData(formData) {
  return (dispatch) => {
    dispatch({
      type: "EMPTY_DATA",
      payload: { data: "", status: 200, message: "" },
    });
  };
}
