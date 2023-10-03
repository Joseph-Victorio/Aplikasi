export default function () {
  return {
    loggedUser: false,
    user: null,
    address: JSON.parse(localStorage.getItem('user_address')) || []
  }
}
