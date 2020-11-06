<template>
    <div class="c-modal">
        <div class="c-modal__window">
            <i class="fas fa-times c-modal__title--icon" @click="changeModal"></i>
            <template v-if="!twitterScreenName">
                <scheduledAccount />
            </template>
            <template v-else>
                <div class="c-modal__title--text u-margin__modal__title--left" >
                    <span @click="changeSettingFlag">予約設定</span>
                    /
                    <span @click="changeListFlag">予約一覧</span>
                </div>
                <scheduledPost v-if="componentsFlag" />
                <scheduledList v-else/>
            </template>
        </div>
    </div>
</template>

<script>
import scheduledPost from './ScheduledParts/scheduledPost'
import scheduledList from './ScheduledParts/scheduledList'
import scheduledAccount from './ScheduledParts/scheduledAccount'
import { mapState } from 'vuex'

export default {
    data(){
        return{
            componentsFlag:true
        }
    },
    components:{
        scheduledPost,
        scheduledList,
        scheduledAccount
    },
    methods:{
        //予約画面を閉じる
        changeModal(){
            this.$store.commit('modal/isModalShow');
        },
        /**
         * コンポーネントを表示する。
         */
        changeSettingFlag(){
            this.componentsFlag = true
        },
        changeListFlag(){
            this.componentsFlag = false
        },
    },
    created(){
        this.$store.dispatch('modal/resetSetting')
        this.$store.commit('modal/setScreenName','')
        window.scroll(0, 0);
    },
     computed:{
        ...mapState({
            twitterScreenName: state => state.modal.twitterScreenName,
        }),
    },
}
</script>