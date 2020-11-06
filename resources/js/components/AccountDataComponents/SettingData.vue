<template>
    <div class="p-data__wrap">
        <DataTargetList/>
        <DataFollowerSearch/>
        <DataUnfollow/>
        <DataLike/>
    </div>
</template>
<script>
import Loading from '../Loading.vue'
import { OK } from '../../util'
import DataTargetList from './SettingDataParts//DataTargetList'
import DataFollowerSearch from './SettingDataParts/DataFollowerSearch'
import DataUnfollow from './SettingDataParts/DataUnfollow'
import DataLike from './SettingDataParts/DataLike'

export default {
    data(){
        return{
            flag:{
                follow_flag:'',
                follow_target_flag:'',
                unfollow_flag:'',
                unfollow_target_flag:'',
                like_flag:''
            },
            followNum:0,
            unfollowNum:0,
        }
    },
    components:{
        Loading,
        DataTargetList,
        DataFollowerSearch,
        DataUnfollow,
        DataLike,
    },
    methods:{
        //URLのパラメータを書き換えて自分以外のツイッターアカウントにしていないかをチェック.
        async parameCheck($twitter_screen_name){
            const response = await axios.post('/api/parameCheck',
            {
                'twitter_screen_name':$twitter_screen_name
            })
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status)
                exit;
            }
        },
    },
    created:function (){
        this.$store.dispatch('error/errorCheck')
    },
}
</script>