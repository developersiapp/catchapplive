import axios from "axios";
import moment from "moment";

export function isLoggedIn() {
  return localStorage.getItem("isLoggedIn");
}

export function userType() {
  return localStorage.getItem("user-type");
}

export function getUser() {
  return localStorage.getItem("userData");
}

export function setToken(access_token) {
  localStorage.setItem("access_token", access_token);
  axios.defaults.headers.common["access-token"] = getToken();
  return true;
}

export function getToken(access_token) {
  let token = localStorage.getItem("access_token");
  if (token) {
    return token;
  } else return false;
}

export function logout() {
  let userType = localStorage.getItem("user-type");
  clearToken();
  clearUserData();
  if (userType == "user") {
    window.location.href = "/";
  } else {
    window.location.href = "/";
  }
  return;
  //const logoutInstance = axios.create();
  //logoutInstance.defaults.headers.common["access-token"] = getToken();
  /*  logoutInstance
    .get(url + "user/logout")
    .then(response => {
      clearToken();
      clearUserData();
      window.location.href = "/login";
      return;
    })
    .catch(error => {
      clearToken();
      clearUserData();
      window.location.href = "/login";
    });*/
}

export function clearToken() {
  localStorage.removeItem("access_token");
  localStorage.removeItem("isLoggedIn");
  localStorage.removeItem("user_type");

  return;
}

export function clearUserData() {
  localStorage.removeItem("userData");
  localStorage.removeItem("isLoggedIn");
  //localStorage.removeItem("clubLocation");

  return;
}

export function isFormSubmit(interval) {
  interval = interval || 2;
  let isFormSubmit = true;
  let onSubmitTime = localStorage.getItem("onSubmitTime");
  if (onSubmitTime) {
    let currentTime = moment();
    if (currentTime.diff(moment.unix(onSubmitTime), "seconds") <= interval) {
      isFormSubmit = false;
    } else {
      localStorage.setItem("onSubmitTime", moment().format("X"));
    }
  } else {
    localStorage.setItem("onSubmitTime", moment().format("X"));
  }
  return isFormSubmit;
}
