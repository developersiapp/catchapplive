import { combineReducers } from "redux";
import { routerReducer } from "react-router-redux";
import DashboardReducer from "./DashboardReducer.js";
import LoginReducer from "./LoginReducer.js";
import StreamReducer from "./StreamReducer.js";

export default combineReducers({
  DashboardReducer,
  LoginReducer,
  StreamReducer,
  routing: routerReducer
});
