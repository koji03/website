<template>
    <transition name="p-fade">
        <div class="u-margin__fade--top" v-show="isForm">
            <Loading v-show="likeLoad" />
            <LikeChangeBtn v-show="!likeLoad" />
            <RegisterLikeWord v-show="!likeLoad" />
            <LikeWordBox  class="u-margin__search--top" v-show="!likeLoad" />
            <LikeNote/>
        </div>
    </transition>
</template>
<script>
import { mapState } from 'vuex'
import LikeChangeBtn from './LikeFormParts/LikeChangeBtn'
import RegisterLikeWord from './LikeFormParts/RegisterLikeWord'
import LikeWordBox from './LikeFormParts/LikeWordBox'
import LikeNote from './LikeFormParts/LikeNote'
import Loading from '../../../../Loading.vue'

export default {
     components:{
        Loading,
        LikeChangeBtn,
        RegisterLikeWord,
        LikeWordBox,
        LikeNote
    },
    computed:{
        ...mapState({
            isForm: state => state.autoActionSetting.isLikeOpen,
            likeLoad: state => state.autoActionSetting.likeLoad
        }),
    },
}
</script>