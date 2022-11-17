import React, { Component } from "react";
import "./App.css";
import { Redirect, BrowserHistory, Switch } from "react-router";
import {
  BrowserRouter as Router,
  Route,
  Link,
  withRouter,
} from "react-router-dom";
import Login from "./Components/Login/Login.js";
import UserLogin from "./Components/Login/UserLogin.js";
import Dashboard from "./Components/Dashboard/Dashboard.js";
import UserDashboard from "./Components/Dashboard/Dashboard.js";
import ForgotPassword from "./Components/ForgotPassword/ForgotPassword.js";
import UserClubDeatils from "./Components/Dashboard/UserClubDeatils.js";
import ResetPassword from "./Components/ForgetPassword/ResetPassword";
import AboutUs from "./Components/AboutUS/AboutUs";

class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isLoggedIn: false,
    };
    if (localStorage.getItem("isLoggedIn") == 1) {
    }
  }

  componentDidMount() {}

  render() {
    return (
      <Router>
        <Switch>
          <Route path="/dashboard" component={Dashboard} />
          <Route exact path="/" component={UserDashboard} />
          <Route path="/dj-login" component={Login} />
          <Route path="/user-login" component={UserLogin} />
          <Route path="/about-us" component={AboutUs} />
          <Route path="/forget-password" component={ForgotPassword} />
          <Route exact path="/user-club/:id" component={UserClubDeatils} />
          <Route
            exact
            path="/reset-password/:token"
            component={ResetPassword}
          />
          <Redirect from="/*" to="/" />
        </Switch>
      </Router>
    );
  }
}
export default withRouter(App);
