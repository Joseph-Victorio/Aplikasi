export function SET_USER(state, payload) {
  state.user = payload
  state.loggedUser = true
}

export function LOGOUT(state) {
  state.user = null
  state.loggedUser = false
}
export function DELETE_ADDRESS(state, id) {
  state.address = state.address.filter(el => el.id != id)
}
export function PUSH_ADDRESS(state, payload) {
  if (payload.is_primary) {
    for (let i = 0; i < state.address.length; i++) {
      state.address[i].is_primary = false
    }
  }
  const idx = state.address.findIndex(el => el.id == payload.id)
  if (idx >= 0) {
    state.address[idx] = payload
  } else {

    state.address.unshift(payload)
  }
  localStorage.setItem('user_address', JSON.stringify(state.address))
}
