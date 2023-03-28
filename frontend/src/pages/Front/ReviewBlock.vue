
<script>
export default {
    props: ['reviews']
}
</script>

<template>
    <div>
        <q-chat-message v-for="review in reviews" :key="review.id"
           text-color="dark"
           bg-color="grey-2"
           class="q-mb-md"
         >
           <template v-slot:name>
            <div class="row q-gutter-x-md q-mb-xs">
                <div class="text-weight-medium">{{ review.name }} </div>
                <q-rating 
                    data-nosnippet="true"
                    readonly
                    v-model="review.rating"
                    color="accent"
                    icon="ion-star-outline"
                    icon-selected="ion-star"
                    icon-half="ion-star-half"
                    size="1.1rem"
                />
            </div>
        </template>
           <template v-slot:stamp>{{ review.created }} </template>
           <template v-slot:avatar>
            <q-avatar icon="eva-person-outline" color="grey-3" text-color="grey-7" class="q-mx-sm"></q-avatar>
           </template>
           <div>
                <div v-if="!review.is_approved" class="text-xs text-orange">( Menunggu Moderasi )</div>
                <div class="q-mt-sm">{{ review.comment }}</div>
           </div>
         </q-chat-message>
    </div>
</template>