<template>
    <div class="u-margin--top__large u-margin--bottom__large">
        <template v-if="this.buttonFlag !== this.startFlag.suspend">
            <Setting />
            <div class="p-cp_ipcheck" v-if="this.buttonFlag !== this.startFlag.restart">
                ON/OFF設定
                    <input type="checkbox" name="setting" value="follow" id="follow" checked v-model="checkbox.follow" class="p-cp_ipcheck__input p-cp_ipcheck__input__checkbox">
                    <label for="follow" class="p-cp_ipcheck__label">フォロー</label> 
                    <input type="checkbox" class="p-cp_ipcheck__input p-cp_ipcheck__input__checkbox" name="setting" value="unfollow" id="unfollow" checked v-model="checkbox.unfollow">
                    <label for="unfollow" class="p-cp_ipcheck__label">アンフォロー</label>
                    <input type="checkbox" class="p-cp_ipcheck__input p-cp_ipcheck__input__checkbox" name="setting" value="like" id="like" checked v-model="checkbox.like">
                    <label for="like" class="p-cp_ipcheck__label">いいね</label>
            </div>
        </template>
        <Error/>
        <template v-if="
            !accountListLoad && 
            !targetListFormLoad && 
            !followerSearchLoad &&
            !unfollowLoad &&
            !likeLoad&&
            !startLoad">

                <Start v-if="
                this.buttonFlag === this.startFlag.start || this.buttonFlag === this.startFlag.update" :checkbox="checkbox"/>
                <Restart v-else-if="this.buttonFlag === this.startFlag.restart" :checkbox="checkbox"/>
                <Suspend v-else-if="this.buttonFlag === this.startFlag.suspend"/>
                
        </template>
        <Loading v-else/>
        <Summary/>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import Setting from './SettingComponents/Setting'
import Summary from '../Summary'
import {OK} from '../../util'
import Loading from '../Loading'
import Error from './StartComponents/Error'
import Start from './StartComponents/ButtonComponents/Start'
import Restart from './StartComponents/ButtonComponents/Restart'
import Suspend from './StartComponents/ButtonComponents/Suspend'
export default {
    data(){
        return{
            checkbox:{
                follow:true,
                unfollow:true,
                like:true,
            },
        }
    },
    components:{
        Setting,
        Summary,
        Loading,
        Error,
        Start,
        Suspend,
        Restart
    },
    computed:{
        ...mapState({
            accountListLoad: state => state.twitter.accountListLoad,
            targetListFormLoad: state => state.autoActionSetting.targetListFormLoad,
            followerSearchLoad: state => state.autoActionSetting.followerSearchLoad,
            unfollowLoad: state => state.autoActionSetting.unfollowLoad,
            likeLoad: state => state.autoActionSetting.likeLoad,

            twitter_screen_name: state => state.twitter.twitter_screen_name,

            startFlag: state => state.autoActionStart.startFlag,
            buttonFlag: state => state.autoActionStart.buttonFlag,
            startLoad: state => state.autoActionStart.startLoad,
        }),
    },
}
</script>