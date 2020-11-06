<template>
    <transition name="p-fade">
    <div class="u-margin__fade--top" v-show="isForm">
        <Loading v-show="unfollowLoad" />
        <div v-show="!unfollowLoad">
            <UnfollowSettingRegister/>
            <note/>
        </div>
    </div>
    </transition>
</template>
<script>
import UnfollowSettingRegister from './FormParts/UnfollowSettingRegister'
import note from './FormParts/UnfollowNote'
import { mapState } from 'vuex'
import { OK } from '../../../../../util'
import Loading from '../../../../Loading.vue'
export default {
    components:{
        UnfollowSettingRegister,
        note,
        Loading
    },
    computed:{
        ...mapState({
            isForm: state => state.autoActionSetting.isUnfollowOpen,
            unfollowLoad: state => state.autoActionSetting.unfollowLoad,
        }),
    }
}
</script>