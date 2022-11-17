import React, { Component } from "react";

export default class Footer extends Component {
  render() {
    return (
      <div>
        <div className={"container"}>
          <div className="row">
            <div className="col-12 copyright text-center">
              Copyright © 2020 <span className="color-footer">CatchApp</span> |
              All Rights Reserved{" "}
            </div>
          </div>
        </div>
      </div>
    );
  }
}
