				<div class="cBoxHeader"><h2>{$title}</h2></div>
				<div class="cBoxBg">
					<div class="cBoxText">
						<div class="cBoxTextInfo">
							<ol>
								<li><span class="what">Posted By:</span> <span class="bold">{$username}</span></li>
								<li><span class="what">On:</span> {$date}</li>
							</ol>
						</div>

<b>Current Rating:</b> {$current_rating}<br />
<b>Rate Content:</b> {$rating_form}<br><br>

{$review_contents}<br />

</div>
					</div>
				</div>

{$comments_form}<br />

<b>Comments</b><br />
<div id="comments">
{section name=r loop=$comments}
<table class="newstxt" cellpadding="5" cellspacing="2" border="0" style="border: 2px solid #868585" width="100%"><tr><td bgcolor="#868585"> {$comments_username[r]}, {$comments_date[r]}</td></tr><tr><td>{$comments_comment[r]}</td></tr><tr><td bgcolor="#868585"><b>Rating:</b> {$comments_rating[r]}, <b>Rate Comment:</b> {$comments_rating_form[r]}</td></tr></table><br />
{/section}
</div>