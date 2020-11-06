<template>
    <div class="u-list__background__color">
        <div class="c-setting__wrap">
            <Button/>
            <UnfollowForm/>
        </div>
    </div>
</template>
<script>
import Button from './UnfollowParts/UnfollowButton'
import UnfollowForm from './UnfollowParts/UnfollowForm'
import { mapState } from 'vuex'
import { OK } from '../../../../util'
import Loading from '../../../Loading.vue'

export default {
    data(){
        return{
            
        }
    },
    components:{
        Button,
        UnfollowForm
    },
    methods:{
        //DBから取得しtwitter.jsとautoActionSettinh.jsに格納
        async getUnfollowSetting(){
            this.$store.commit('autoActionSetting/setUnfollowLoad',true)
            const response = await axios.post('/api/getUnfollowSetting',{
                twitter_screen_name:this.twitter_screen_name
            })
            this.$store.commit('autoActionSetting/setUnfollowLoad',false)
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status)
            }
            const days = response.data.days
            const people = response.data.people

            //unfollowのformに表示するために使う。
            this.$store.commit('autoActionSetting/setSettingUnfollowDays',days)
            this.$store.commit('autoActionSetting/setSettingUnfollowPeople',people)

            //start.vueでアンフォロー設定が存在するかのチェックで使う
            this.$store.commit('twitter/setUnfollowPeople',people)
            this.$store.commit('twitter/setUnfollowDays',days)
        },
        
    },
    computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
        }),
    },
    created(){
        this.getUnfollowSetting()
    },
    watch:{
        '$route':function(){
            this.getUnfollowSetting()
        }
    }
}
</script>