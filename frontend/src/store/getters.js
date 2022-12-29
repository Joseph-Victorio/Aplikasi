
const getApiUrl = (state) => {
	return state.API_URL
}

const getTheme = (state) => {
  if(state.config && state.config.theme) {
    return state.config.theme
  }
  return 'default'
}

export default {
  getApiUrl,
  getTheme,
}