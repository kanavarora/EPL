<fb:iframe src="http://ads.socialmedia.com/facebook/monetize.php?width=645&height=60&pubid=65b9db3f78ed23142d551b3f12f7f01b" border="0" width="645" height="60" scrolling="no" frameborder="0"></fb:iframe>

<?php
require_once('dbappinclude.php') ;
require_once('header.php') ;
?>
<table table border="5" bordercolor=#3b5998 width="100%" > 
<table width="100%" cellpadding="0" bgcolor="#F7F7F7">


<tr>
<td width="85%" valign="top">


<b>Q. What is this application all about?</b><br />
<b>A.</b> This application basically helps you follow all the English Premiere League Football action. By clicking on the team
pages like on the left side of this page, you can watch the video highlights of the teams recent EPL matches. Moreover,
you could go to the <a href="edit.php">Edit profile</a> page to create your very own <a href="profile.php">EPL profile</a>. 
This is very important as it will help us know more about you and you will see pages cutomized according to your preferences.<br/>
<br/>
<b>Q. Which all matches can I follow on this application?</b><br/>
<b>A.</b> You can follow all your favorite English Premiere League teams not only in the Barclays Premiere League, but also in the 
FA Cup, Carling Cup and the Uefa Champions League. So, if you are supporting Manchester United, you can follow all its club matches
in EPL, the FA cup and even the Uefa (Too bad, they are out of the Carling Cup). Besides these matches, I will also try to post some 
special videos on the <a href="classified.php">Classified</a> page. These videos might not be related to EPL at all but trust me, you will
definitely like them if you like football.<br/> 
<br/>
<b>Q. How does the video searching work? </b><br/>
<b>A.</b> As you might know, manually searching and posting the links 
for all the matches might be a pain. What might be even tougher is to constantly update those links as a lot of the videos posted get blocked
due to copyright issues. So we have written an automated Algorithm which searches uses some publicly available API's 
and searches for the videos of the matches. The algorithm tries to ensure that only quality videos (that means no spammers) are shown on this application.
There is a chance that our algorithm might not be able to search for the best video, that&#039s why, we show upto 4 video results, 
so that the chances of getting good results are increased. Moreover, this algorithm is run hourly, so it constantly updates broken links and searches for new and better videos.
We have included and considered a lot of parameters to ensure that the results shown are among the best.
Still if you think you could somehow improve the algorithm and the search results, we would love to hear your opinion.<br/>
<br/>
<b>Q. I can&#039t find the video highlights for so-and-so match?</b><br/>
<b>A.</b> If our algorithm cant find the video highlights for the so-and-so match, chances are that either the so-and-so match just took place and it will take time for the results to be shown 
or there aren&#039t many (or maybe even any) videos on the net for that match (atleast free videos). Our algorithm relies on videos posted by other people on the net, so
if the pool of data to search for is inadequate, there&#039s not much our algorithm could do in that case. However, if any of you guys can find a video highlight for that match, 
please share it with us, so everyone can view it. You wont receive any accolade for it, but Ill try to post the contributor's name in the description for the video :)<br/>
<br/>
<b>Q. The link provided for the video highlight is broken????</b><br/>
<b>A.</b> There are a lot of copyright issues associated with these videos. So it might have been deleted for that reason. So just wait for some time for the links to update and hopefully, it
shouldn&#039t be there. Or in the meantime you could try viewing other video highlights for the same match. That&#039s why we provide upto 4 results.<br/>
<br/>
<b>Q. When do the video highlights links for the matches get posted?</b><br/>
<b>A.</b> We try to make run the algorithm before the match even starts. So if it is a popular match, you might even get video results as the match is going along. But it all depends, on the pool 
of data to search for.<br/>
<br/>
<b>Q. I see EPL written all over? What does it stand for?</b><br/>
<b>A.</b> I thought EPL would be a pretty popular abbreviation. Anyways it stands for English Premiere League.<br/>
<br/>


</td>
<td width="2" bgcolor= "#0000FF"> </td> 
<td  valign="top">
<?php
include ('frame.php') ;
?>
</td>

</tr>
</table>
</table>

