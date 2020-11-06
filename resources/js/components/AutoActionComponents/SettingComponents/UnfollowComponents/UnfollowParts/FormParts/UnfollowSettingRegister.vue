<template>
    <form action="" class="c-setting__form" @submit.prevent="regist" novalidate>
        <DaysInput @days="errorCheck" :message="message"/>
        <PeopleInput @people="errorCheck" :message="message"/>
        <input  type="submit" class="c-number__form u-margin__number__button--top u-color__unfollow__btn" value="登録" v-bind:style="{ backgroundColor: btnColor }" v-bind:disabled="clickFlag"
        >
    </form>
</template>
<script>
import { mapState, mapGetters } from 'vuex'
import { OK } from '../../../../../../util'
import DaysInput from './SettingParts/DaysInput'
import PeopleInput from './SettingParts/PeopleInput'
export default {
    data(){
        return{
            btnColor :'',
            clickFlag:true,
            message:{
                moreNumber:'7以上の数字を入力してください',
                underNumber:'1000以下の数字を入力してください',
                integer:'整数を入力してください',
                firstNumber:'先頭の数字は0以外にしてください',
                number:'数字を入力してください'
            },
        }
    },
    components:{
        DaysInput,
        PeopleInput
    },
    methods:{
        //子コンポーネントから渡ってきたときにエラーがなくdaysとpeopleの両方に値が入力されていたらbuttonのカラーを変える。
        errorCheck:function(){
            this.btnColor=""
            this.clickFlag = true
            if(this.settingUnfollowDays == null || this.settingUnfollowPeople == null){
                return false
            }
            if(this.daysError >= 0  && this.peopleError >= 0){
                this.btnColor="#ff6664"
                this.clickFlag = false
            }
        },
        //アンフォローの設定を保存する。
        async regist(){
            this.btnColor = '',
            this.$store.commit('autoActionSetting/setUnfollowLoad',true)
            const array = {days:this.settingUnfollowDays,people:this.settingUnfollowPeople}
            const response = await axios.post('/api/saveUnfollowSetting',{
                number : array,
                twitter_screen_name:this.twitter_screen_name
            })
            this.$store.commit('autoActionSetting/setUnfollowLoad',false)
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status)
                this.$store.commit('autoActionSetting/setPeopleError','エラーが発生しました。ブラウザを更新後、再度試してください。')
            }
            //start.vueでアンフォロー設定が存在するかのチェックで使う
            this.$store.commit('twitter/setUnfollowPeople',this.settingUnfollowPeople)
            this.$store.commit('twitter/setUnfollowDays',this.settingUnfollowDays)
        },
    },
    computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
            daysError: state => state.autoActionSetting.daysError,
            peopleError: state => state.autoActionSetting.peopleError,
            settingUnfollowDays: state => state.autoActionSetting.settingUnfollowDays,
            settingUnfollowPeople: state => state.autoActionSetting.settingUnfollowPeople,
        }),
    },
    created(){
        this.btnColor = ''
    }
}
</script>