				<title>{$sitename} - {$title}</title>
				<h2>{$title}</h2>
				<span class="what">Posted By:</span> <span class="bold">{$username}</span><br />
								<span class="what">On:</span> {$date}<br />
							

<b>Current Rating:</b> {$current_rating}<br />
<b>Rate Content:</b> {$rating_form}<br><br>

{$description}<br /><br />{$full_story}<br />

{$comments_form}<br />

<b>Comments</b><br />
<div id="comments">
{section name=r loop=$comments}
<table class="newstxt" cellpadding="5" cellspacing="2" border="0" style="border: 2px solid #868585" width="100%"><tr><td bgcolor="#868585"> {$comments_username[r]}, {$comments_date[r]}</td></tr><tr><td>{$comments_comment[r]}</td></tr><tr><td bgcolor="#868585"><b>Rating:</b> {$comments_rating[r]}, <b>Rate Comment:</b> {$comments_rating_form[r]}</td></tr></table><br />
{/section}
</div>