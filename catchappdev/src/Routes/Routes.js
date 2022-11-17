import React from "react";
import { Route, Switch, Redirect } from "react-router-dom";
import { Link } from "react-router-dom";
import { isLoggedIn } from "../Utils/services.js";
import Dashboard from "../Components/Dashboard/Dashboard.js";
import Login from "../Components/Login/Login.js";
import UserLogin from "../Components/Login/UserLogin.js";
import Profile from "../Components/Profile/Profile.js";
import ClubDetails from "../Components/Dashboard/ClubDetails.js";
import UserDashboard from "../Components/Dashboard/UserDashboard.js";
import UserClubDeatils from "../Components/Dashboard/UserClubDeatils.js";

const PrivateRoute = ({ component: Component, ...rest }) => (
  <Route
    {...rest}
    render={props =>
      isLoggedIn() ? <Component {...props} /> : <Redirect to="/" />
    }
  />
);

class ErrorBoundary extends React.Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false };
  }

  render() {
    if (this.state.hasError) {
      // Rendering error page
      return (
        <div className="main protected">
          <div className="something-wrong">
            {/*<img src="/images/something-wrong.png" />*/}
            <Link to="/logout" className="click-logout">
              Click to Logout
            </Link>
          </div>
        </div>
      );
    } else {
      return this.props.children;
    }
    //return this.props.children;
  }
}
const router = (
  <ErrorBoundary>
    <Switch>
      <Redirect exact path="/" to="/" />
      <PrivateRoute path="/dashboard" component={Dashboard} />
      <PrivateRoute exact path="/profile" component={Profile} />
      <PrivateRoute exact path="/club-details/:id" component={ClubDetails} />
    </Switch>
  </ErrorBoundary>
);

export default router;
