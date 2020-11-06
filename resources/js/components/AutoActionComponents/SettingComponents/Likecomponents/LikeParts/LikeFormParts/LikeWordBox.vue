<template>
<getOr v-if="currentLikeForm === 'or'"/>
<getNg v-else-if="currentLikeForm === 'ng'"/>
<getNormal v-else-if="currentLikeForm === 'normal'"/>
</template>
<script>
import getOr from './LikeWordBoxComponents/getOr'
import getNg from './LikeWordBoxComponents/getNg'
import getNormal from './LikeWordBoxComponents/getNormal'
import { mapState } from 'vuex'

export default {
    components:{
        getOr,
        getNg,
        getNormal,
    },
    computed:{
        ...mapState({
            currentLikeForm: state => state.autoActionSetting.currentLikeForm,
        }),
    },
}
</script>