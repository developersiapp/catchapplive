import "react-app-polyfill/ie11";
import "react-app-polyfill/stable";
import React from "react";
import ReactDOM from "react-dom";
import thunk from "redux-thunk";
import reducer from "./Reducers/IndexReducer";
import { Provider } from "react-redux";
import { createStore, applyMiddleware } from "redux";
import { Redirect, BrowserHistory, Switch } from "react-router";
import { Router, Route, Link, withRouter } from "react-router-dom";
import * as serviceWorker from "./serviceWorker";
import "./index.css";
//import App from "./App";
import Login from "./Components/Login/Login.js";
import UserLogin from "./Components/Login/UserLogin.js";
import ForgotPassword from "./Components/ForgetPassword/ForgetPassword.js";
import ResetPassword from "./Components/ForgetPassword/ResetPassword.js";
import SignUp from "./Components/SignUp/SignUp.js";
import SignupUser from "./Components/SignUp/SignupUser.js";
import { createBrowserHistory } from "history";
import Routes from "./Routes/Routes.js";
import Logout from "./Components/Login/Logout.js";
import Home from "./Components/Login/Home.js";
import UserDashboard from "./Components/Dashboard/UserDashboard.js";
import UserClubDeatils from "./Components/Dashboard/UserClubDeatils.js";
import { isLoggedIn, getUser } from "./Utils/services";
import "./Assets/common.css";
import "./Assets/common2.scss";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import AboutUs from "./Components/AboutUS/AboutUs";
import "react-loader-spinner/dist/loader/css/react-spinner-loader.css";

const store = createStore(reducer, applyMiddleware(thunk));

function auth() {
  let user = JSON.parse(getUser());
  return isLoggedIn();
}

const PublicRoute = ({ component: Component, ...rest }) => (
  <Route
    {...rest}
    render={(props) =>
      !auth() ? (
        <Component {...props} />
      ) : (
        <Redirect
          to={{
            pathname: "/",
            state: { from: props.location },
          }}
        />
      )
    }
  />
);

const history = createBrowserHistory();
// Get the current location.
const location = history.location;

ReactDOM.render(
  <Provider store={store}>
    <Router history={history}>
      <Switch>
        <Route path="/dj-login" component={Login} />
        <Route path="/user-login" component={UserLogin} />
        <Route path="/home" component={Home} />
        <PublicRoute path="/forget-password" component={ForgotPassword} />
        <Route exact path="/logout" component={Logout} />
        <PublicRoute path="/sign-up/" component={SignUp} />
        <PublicRoute path="/register" component={SignupUser} />
        <Route exact path="/" component={UserDashboard} />
        <Route exact path="/about-us" component={AboutUs} />
        <Route exact path="/user-club/:id" component={UserClubDeatils} />

        <PublicRoute
          exact
          path="/reset-password/:token"
          component={ResetPassword}
        />
        {Routes}
        <Redirect from="/*" to="/" />
      </Switch>
    </Router>
    <ToastContainer
      position="bottom-right"
      autoClose={5000}
      hideProgressBar={false}
      newestOnTop={false}
      closeOnClick
      rtl={false}
      pauseOnVisibilityChange
      draggable
      pauseOnHover
    />
  </Provider>,
  document.getElementById("root")
);

serviceWorker.unregister();
