import React, { Component } from "react";
import { logout } from "../../Utils/services.js";

class Logout extends React.Component {
  constructor(props) {
    super(props);
    logout();
  }
  render() {
    return null;
  }
}

export default Logout;
