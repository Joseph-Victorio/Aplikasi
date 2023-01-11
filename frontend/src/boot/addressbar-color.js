import { AddressbarColor, Loading, QSpinnerGears } from 'quasar'

export default () => {
  AddressbarColor.set('#1bb90d')
}

Loading.setDefaults({
  spinner: QSpinnerGears,
})