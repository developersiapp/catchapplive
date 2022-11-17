const nameInitialState = {
  action: "",
};
const dashboard = (state = nameInitialState, action) => {
  switch (action.type) {
    case "CLUB_LIST": {
      return { ...state, data: action.payload, action: "CLUB_LIST" };
    }

    case "GET_PROFILE": {
      return { ...state, data: action.payload, action: "GET_PROFILE" };
    }

    case "UPDATE_PROFILE": {
      return { ...state, data: action.payload, action: "UPDATE_PROFILE" };
    }

    case "GET_CLUBDETAILS": {
      return { ...state, data: action.payload, action: "GET_CLUBDETAILS" };
    }

    case "CREATE_STREAM": {
      return { ...state, data: action.payload, action: "CREATE_STREAM" };
    }

    case "START_STREAM": {
      return { ...state, data: action.payload, action: "START_STREAM" };
    }

    case "USER_CLUB_LIST": {
      return { ...state, data: action.payload, action: "USER_CLUB_LIST" };
    }

    case "SEARCH_CLUBS": {
      return { ...state, data: action.payload, action: "SEARCH_CLUBS" };
    }

    case "GET_USER_PROFILE": {
      return { ...state, data: action.payload, action: "GET_USER_PROFILE" };
    }

    case "UPDATE_USER_PROFILE": {
      return { ...state, data: action.payload, action: "UPDATE_USER_PROFILE" };
    }

    case "USER_STORIES": {
      return { ...state, data: action.payload, action: "USER_STORIES" };
    }

    case "ADD_CLUB": {
      return { ...state, data: action.payload, action: "ADD_CLUB" };
    }

    case "LIVE_DJ": {
      return { ...state, data: action.payload, action: "LIVE_DJ" };
    }
    case "TOP_CITIES": {
      return { ...state, data: action.payload, action: "TOP_CITIES" };
    }
    case "SEARCH_CITY": {
      return { ...state, data: action.payload, action: "SEARCH_CITY" };
    }

    case "EMPTY_DATA": {
      return { ...state, data: action.payload, action: "EMPTY_DATA" };
    }
    default:
      return state;
  }
};
export default dashboard;
