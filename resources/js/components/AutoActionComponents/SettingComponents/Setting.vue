<template>
    <section class="">
        <h1 class="c-title u-margin__setting__title">
            <i class="fas fa-cogs u-margin__setting__icon"></i>
            自動機能<br>
            @{{twitter_screen_name}}</h1>
        <TargetList/>
        <FollowerSearch/>
        <Unfollow/>
        <Like/>
    </section>
</template>
<script>
import TargetList from'./TargetListComponent/TargetList'
import FollowerSearch from'./FollowsearchComponents/FollowerSearch'
import Unfollow from'./UnfollowComponents/Unfollow'
import Like from'./Likecomponents/Like'
import { mapState } from 'vuex'
export default {
    components:{
        TargetList,
        FollowerSearch,
        Unfollow,
        Like
    },
    computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
        }),
    },
}
</script>