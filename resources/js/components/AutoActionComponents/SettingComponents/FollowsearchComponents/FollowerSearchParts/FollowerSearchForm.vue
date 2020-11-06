<template>
<transition name="p-fade">
    <div class="u-margin__fade--top" v-show="isForm">
        <Loading v-show="followerSearchLoad" />
        <FormChangeBtn v-show="!followerSearchLoad"/>
        <RegisterWord v-show="!followerSearchLoad"/>
        <WordBox class="u-margin__search--top" v-show="!followerSearchLoad"/>
        <FormNote/>
    </div>
</transition>

</template>
<script>
import FormChangeBtn from '../FollowerSearchParts/FormParts/FormChangeBtn'
import RegisterWord from '../FollowerSearchParts/FormParts/RegisterWord'
import WordBox from '../FollowerSearchParts/FormParts/WordBox'
import FormNote from '../FollowerSearchParts/FormParts/FormNote'
import Loading from '../../../../Loading.vue'
import { mapState } from 'vuex'
export default {
    data(){
        return{
        }
    },
    components:{
        FormChangeBtn,
        RegisterWord,
        WordBox,
        FormNote,
        Loading,
    },
    computed:{
        ...mapState({
            followerSearchLoad: state => state.autoActionSetting.followerSearchLoad,
            isForm: state => state.autoActionSetting.isFollowSearchOpen,
        }),
    },
}
</script>