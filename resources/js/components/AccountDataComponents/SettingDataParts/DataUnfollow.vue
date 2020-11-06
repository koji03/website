<template>
    <div class="p-data__box">
            <div class="p-data__box__title">
                アンフォロー
            </div>
        <Loading v-if="unfollowLoad" />
        <div class="p-data__box__list" v-else>
            <ul  class="p-data__box__main u-padding__data">
            <li class="u-margin__data--min">
               <template v-if="number.days === ''">---</template> {{number.days}}日</li>
            <li class="u-margin__data--min">
            <template v-if="number.days === ''">---</template> {{number.people}}人</li>
        </ul>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import Loading from '../../Loading.vue'
import { OK } from '../../../util'
export default {
    data(){
        return{
            number:{
                days:'',
                people:''
            },
        }
    },
    computed:{
        ...mapState({
            unfollowLoad: state => state.autoActionSetting.unfollowLoad,
        }),
    },
    components:{
        Loading
    },
    methods:{
        //dbから取得して画面に表示させる.
        async getUnfollowSetting(){
            const response = await axios.post('/api/getUnfollowSetting',{
                twitter_screen_name:this.$route.params.screen_name
            })
            this.$store.commit('autoActionSetting/setUnfollowLoad',false)
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status)

            }
            const days = response.data.days
            const people = response.data.people
            this.$store.commit('autoActionSetting/setSettingUnfollowDays',days)
            this.$store.commit('autoActionSetting/setSettingUnfollowPeople',people)
            if(String(days) === 'undefined'){
                this.number.days = ''
            }else{
                this.number.days = String(days)
            }
            if(String(people) === 'undefined'){
                this.number.people = ''
            }else{
                this.number.people = String(people)
            }
        },
    },
    created(){
        this.getUnfollowSetting()
    },
    watch:{
        '$route':async function(){
            this.getUnfollowSetting()
        }
    }
}
</script>