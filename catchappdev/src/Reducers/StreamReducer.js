const nameInitialState = {
  action: ""
};
const stream = (state = nameInitialState, action) => {
  switch (action.type) {
    case "CREATE_STREAM": {
      return { ...state, data: action.payload, action: "CREATE_STREAM" };
    }

    case "START_STREAM": {
      return { ...state, data: action.payload, action: "START_STREAM" };
    }

    case "STREAM_STATE": {
      return { ...state, data: action.payload, action: "STREAM_STATE" };
    }

    case "STOP_STREAM": {
      return { ...state, data: action.payload, action: "STOP_STREAM" };
    }

    case "SET_STREAM_DETAILS": {
      return { ...state, data: action.payload, action: "SET_STREAM_DETAILS" };
    }

    case "EMPTY_DATA": {
      return { ...state, data: action.payload, action: "EMPTY_DATA" };
    }
    default:
      return state;
  }
};
export default stream;
