<template>
<getOr v-if="currentForm === 'or'"/>
<getNg v-else-if="currentForm === 'ng'"/>
<getNormal v-else-if="currentForm === 'normal'"/>
</template>
<script>
import getOr from './WordBoxComponent/OrWords'
import getNg from './WordBoxComponent/NgWords'
import getNormal from './WordBoxComponent/NormalWords'
import { mapState } from 'vuex'

export default {
    components:{
        getOr,
        getNg,
        getNormal,
    },
    computed:{
        ...mapState({
            currentForm: state => state.autoActionSetting.currentForm,
        }),
    },
}
</script>