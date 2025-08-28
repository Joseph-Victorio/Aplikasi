<template>
   <div class="relative">
      <div class="list-box-content" v-show="showList">
         <q-list separator>
            <q-item v-for="(list, i) in lists" :key="i" clickable @click="selectItemList(list)">
               <q-item-section>{{ list.label }}</q-item-section>
            </q-item>
            <q-item v-if="notFound">
               <q-item-section class="text-center">Data tidak ditemukan</q-item-section>
            </q-item>
         </q-list>
      </div>
      <div class="relative">
         <div id="clientMapContiner">
         </div>
         <!-- <q-btn flat icon="ion-locate" :loading="loading" class="bg-white text-grey-7 btn-floating" round dense
            padding="sm" @click="getCurrentPosition"></q-btn> -->
      </div>
   </div>
</template>

<script>
import "leaflet/dist/leaflet.css";

import L from 'leaflet'
import { OpenStreetMapProvider } from 'leaflet-geosearch';
import 'leaflet-geosearch/dist/geosearch.css';

export default {
   props: ['coordinate', 'config'],
   data() {
      return {
         map: null,
         zoom: 11,
         searchControl: null,
         originMarker: null,
         destinationMarker: null,
         destinationCenter: null,
         originMarkerOption: {
            clickable: false,
            draggable: false,
            icon: null
         },
         destinationMarkerOption: {
            clickable: true,
            draggable: true,
            autoPan: true,
            icon: null
         },
         query: '',
         provider: null,
         lists: [],
         listSelected: null,
         searchLoading: false,
         notFound: false,
         totalDistanceKm: 0,
         totaldistanceMinutes: 0,
         freeOngkirUnder: 2,
         hargaJarakPerKm: 500,
         loading: false,
         lastLocalCost: 0,
         maxDistance: 0,
         user_coordinate: [],
         warehouse_coordinate: [],
         polyline: null,
         default_center: [-7.969945177009877, 110.6048905849457],
      }
   },
   mounted() {
      this.$nextTick(() => {
         setTimeout(() => {
            this.initialMap()
         }, 700)
      })
   },
   beforeUnmount() {
      this.map.remove()
   },
   watch: {
      totalDistanceKm: function () {
         this.getLocalCost()
      }
   },
   computed: {
      showList() {
         return this.lists.length || this.notFound
      },
      shop() {
         return this.$store.state.shop
      }

   },
   methods: {
      autoSearch(key) {
         this.provider.search({ query: key }).then(response => {
            if (response && response.length) {

               // console.log(response);

               this.selectItemList(response[0])
            }
         })
      },
      searchQuery() {
         if (this.query.length < 4) return
         this.notFound = false
         this.searchLoading = true
         this.provider.search({ query: this.query }).then(response => {
            this.lists = response
         }).finally(() => {
            this.searchLoading = false
            if (!this.lists.length) {
               this.notFound = true

               setTimeout(() => {
                  this.notFound = false

               }, 5000)
            }
         })

      },
      inputQuery() {
         this.notFound = false
      },
      selectItemList(list) {
         this.listSelected = list
         let latlng = [list.y, list.x]
         this.lists = []

         this.setMapView(latlng)
         this.setDestinationMarker(latlng)

         this.$emit('onSelectAddress', list)

      },
      initialMap() {

         // this.warehouse_coordinate = this.config.warehouse_coordinate

         this.provider = new OpenStreetMapProvider({
            params: {
               countrycodes: 'id'
            }
         });

         this.destinationMarkerOption.icon = L.icon({
            iconUrl: "/static/location.png",
            iconSize: [60, 60],
            iconAnchor: [30, 60],
         })

         if (this.config.warehouse_coordinate && this.config.warehouse_coordinate.length) {
            this.center = this.config.warehouse_coordinate
         }

         this.map = L.map('clientMapContiner', {
            center: this.coordinate.length ? this.coordinate : this.center,
            zoom: this.zoom
         });


         if (this.config && this.config.mapbox_access_token) {

            L.tileLayer(`https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=${this.config.mapbox_access_token}`, {
               attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
               maxZoom: 20,
               id: 'mapbox/streets-v11',
               tileSize: 512,
               zoomOffset: -1,
            }).addTo(this.map);

         } else {

            L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
               attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.map);

         }


         if (this.coordinate && this.coordinate.length) {

            this.setDestinationMarker(this.coordinate)
         }

         this.map.on('click', this.handleMapClicked);


      },
      handleMapClicked(e) {

         let container = L.DomUtil.create('div')

         let btn = L.DomUtil.create('button', '', container);
         btn.setAttribute('type', 'button');
         btn.innerHTML = 'Pilih lokasi disini';

         L.popup()
            .setContent(container)
            .setLatLng(e.latlng)
            .openOn(this.map);
         L.DomEvent.on(btn, 'click', () => {

            this.setDestinationMarker([e.latlng.lat, e.latlng.lng])

         });
      },
      setDestinationMarker(latlng) {

         if (this.polyline) {
            this.map.removeLayer(this.polyline);
            this.polyline = null;
         }
         this.user_coordinate = latlng

         if (this.destinationMarker != undefined) {

            this.map.removeLayer(this.destinationMarker);
         };

         this.destinationMarker = L.marker(latlng, this.destinationMarkerOption);

         this.destinationMarker.addTo(this.map)

         this.map.closePopup();

         this.destinationCenter = latlng

         this.map.setView(latlng)

         this.loading = false

         // let dinMeters = this.map.distance(this.warehouse_coordinate, latlng)

         // let dInkm = (dinMeters / 1000).toFixed(1);

         // console.log('direct distance: in M', dinMeters);
         // console.log('direct distance: in KM', dInkm);

         //draw a line between two points
         // this.polyline = L.polyline([this.warehouse_coordinate, latlng], {
         //    color: 'red'
         // });

         //add the line to the map
         // this.polyline.addTo(this.map);


      },
      setMapView(latlng) {
         setTimeout(() => {
            this.map.setView(latlng)
            this.map.closePopup();
         }, 500)
      },
      getCurrentPosition() {
         this.loading = true
         const options = {
            enableHighAccuracy: true,
            timeout: 8000,
            maximumAge: 0
         };
         if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(this.geoLocationSuccess, this.geoLocationError, options);

         } else {
            this.loding = false
            this.$q.notify({
               type: 'warning',
               message: 'Geolocation is not supported by this browser.'
            })
         }
      },
      geoLocationError() {
         this.loading = false
         this.$q.notify({
            type: 'warning',
            message: 'Lokasi tidak ditemukan, coba ulangi sekali lagi.'
         })
      },
      geoLocationSuccess(position) {
         setTimeout(() => {
            this.setDestinationMarker([position.coords.latitude, position.coords.longitude])
            this.loding = false
         }, 500)
      },
      createPopup() {
         return `<div class="marker-tooltip">
              <div class="details-text">Jarak : <b>${this.totalDistanceKm} Km</b></div>
              <div class="details-text">Estimasi Ongkos Kirim: <b> ${this.formatOngkir()} </b></div>
            </div>`;
      },
      getLocalCost() {

         if (this.config && this.config.local_shipping_costs.length) {

            let localCosts = this.config.local_shipping_costs

            // Get max distance from last item

            let lastItem = localCosts[localCosts.length - 1]

            this.maxDistance = parseInt(lastItem.radius)
            this.hargaJarakPerKm = parseInt(lastItem.cost)
            this.lastLocalCost = parseInt(lastItem.cost)

            // Get Cost by current distance

            for (let i = 0; i < localCosts.length; i++) {

               let currentRule = localCosts[i]
               this.hargaJarakPerKm = parseInt(currentRule.cost)

               if (this.totalDistanceKm >= parseInt(currentRule.radius)) {

                  let nextRule = localCosts[i + 1]


                  if (nextRule && parseInt(nextRule.radius) > this.totalDistanceKm) {

                     this.hargaJarakPerKm = parseInt(currentRule.cost)


                     break
                  }

               }
            }

            if (this.totalDistanceKm > this.maxDistance) {
               // this.$q.dialog({
               //    title: 'Tujuan diluar jangkauan!',
               //    message: `Mohon maaf alamat tujuan diluar jangkauan kurir toko, Maksimal adalah ${this.maxDistance} km.`
               // })

               this.$emit('onError', 'Alamat tujuan diluar jangkauan kurir toko')

               return
            }

            this.$emit('onSelected', {
               amount: this.getOngkir(),
               user_coordinate: this.user_coordinate
            })

            setTimeout(() => {
               if (this.setDestinationMarker != undefined) {
                  this.destinationMarker.bindTooltip(this.createPopup(), {
                     permanent: true,
                     direction: 'top',
                     offset: [0, -40]
                  });
               }
            }, 500)
         }
      },
      getOngkir() {

         if (this.totalDistanceKm > 0) {

            return Math.round(this.totalDistanceKm * this.hargaJarakPerKm)
         }
         return 0;
      },
      formatOngkir() {
         if (this.totalDistanceKm > 0) {

            return this.moneyFormat(this.getOngkir())
         }
         return 'Gratis';
      }
   },
}
</script>

<style scoped>
#clientMapContiner {
   height: 350px;
   width: 100%;
}

.list-box {
   position: relative;
   z-index: 1006;
}

.list-box-content {
   position: absolute;
   width: 100%;
   overflow-y: auto;
   background-color: #ffffff;
   z-index: 1006;
   max-height: 270px;
   height: auto;
}

/* .leaflet-top.leaflet-right {
   display: none;
} */
</style>