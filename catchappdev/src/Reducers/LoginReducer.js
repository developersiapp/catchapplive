const loginReducer = (state = { data: "", status: "" }, action) => {
  switch (action.type) {
    case "CALL_START": {
      return {};
    }
    case "REGISTER_SUCCESSFULL": {
      return { ...state, data: action.payload, action: "REGISTER" };
    }
    case "REGISTER_ERROR": {
      return { ...state, data: action.payload, action: "REGISTER" };
    }

    case "LOGIN_SUCCESSFULL": {
      return { ...state, Logindata: action.payload, action: "LOGIN" };
    }
    case "LOGIN_ERROR": {
      return { ...state, Logindata: action.payload, action: "LOGIN" };
    }

    case "USER_LOGIN_SUCCESSFULL": {
      return { ...state, Logindata: action.payload, action: "USER_LOGIN" };
    }
    case "USER_LOGIN_ERROR": {
      return { ...state, Logindata: action.payload, action: "USER_LOGIN" };
    }

    case "SOCIAL_LOGIN_SUCCESSFULL": {
      return { ...state, Logindata: action.payload, action: "SOCIAL_LOGIN" };
    }
    case "SOCIAL_LOGIN_ERROR": {
      return { ...state, Logindata: action.payload, action: "SOCIAL_LOGIN" };
    }

    case "FORGET_PASSWORD_SUCCESSFULL": {
      return { ...state, data: action.payload, action: "FORGET_PASSWORD" };
    }
    case "FORGET_PASSWORD_ERROR": {
      return { ...state, data: action.payload, action: "FORGET_PASSWORD" };
    }

    case "RESET_PASSWORD_SUCCESSFULL": {
      return { ...state, data: action.payload, action: "RESET_PASSWORD" };
    }
    case "RESET_PASSWORD_ERROR": {
      return { ...state, data: action.payload, action: "RESET_PASSWORD" };
    }

    case "USER_REGISTER_SUCCESSFULL": {
      return { ...state, data: action.payload, action: "USER_REGISTER" };
    }
    case "USER_REGISTER_ERROR": {
      return { ...state, data: action.payload, action: "USER_REGISTER" };
    }

    case "USER_FORGET_PASSWORD_SUCCESSFULL": {
      return { ...state, data: action.payload, action: "USER_FORGET_PASSWORD" };
    }
    case "USER_FORGET_PASSWORD_ERROR": {
      return { ...state, data: action.payload, action: "USER_FORGET_PASSWORD" };
    }

    case "USER_RESET_PASSWORD_SUCCESSFULL": {
      return { ...state, data: action.payload, action: "USER_RESET_PASSWORD" };
    }
    case "USER_RESET_PASSWORD_ERROR": {
      return { ...state, data: action.payload, action: "USER_RESET_PASSWORD" };
    }

    case "USER_SOCIAL_LOGIN_SUCCESSFULL": {
      return {
        ...state,
        Logindata: action.payload,
        action: "USER_SOCIAL_LOGIN",
      };
    }
    case "USER_SOCIAL_LOGIN_ERROR": {
      return {
        ...state,
        Logindata: action.payload,
        action: "USER_SOCIAL_LOGIN",
      };
    }

    case "CHECK_EMAIL": {
      return { ...state, data: action.payload, action: "CHECK_EMAIL" };
    }

    case "USER_CHECK_EMAIL": {
      return { ...state, data: action.payload, action: "USER_CHECK_EMAIL" };
    }

    case "DJ_SOCIAL_REGISTER_SUCCESSFULL": {
      return { ...state, data: action.payload, action: "DJ_SOCIAL_REGISTER" };
    }
    case "DJ_SOCIAL_REGISTER_ERROR": {
      return { ...state, data: action.payload, action: "DJ_SOCIAL_REGISTER" };
    }

    case "USER_SOCIAL_REGISTER_SUCCESSFULL": {
      return { ...state, data: action.payload, action: "USER_SOCIAL_REGISTER" };
    }
    case "USER_SOCIAL_REGISTER_ERROR": {
      return { ...state, data: action.payload, action: "USER_SOCIAL_REGISTER" };
    }

    case "EMPTY_DATA": {
      return { ...state, data: action.payload, action: "EMPTY_DATA" };
    }

    default: {
      return state;
    }
  }
};

export default loginReducer;
