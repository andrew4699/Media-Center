<?php

	require_once("configuration/main.php");

	$_SESSION['unique_id'] = md5(rand());

	$mysql->query("INSERT INTO `record` (`date`, `unique_id`, `srecord`) VALUES ('" . time() . "', '" . $_SESSION['unique_id'] . "', 'WHERE ')");

	if($_COOKIE['mc_volume'])
	{
		echo
		"<script>
			$(document).ready(function()
			{
				$('#playerSong').jPlayer('volume', " . $_COOKIE['mc_volume'] . ");
			});
		</script>";
	}

?>

<div id='playerContainer'>
	<div id='playerSong' class='hidden'></div>
	<div id='playerSongPath' class='hidden'></div>

	<div id='topBarContainer' class='topBarContainerLight'>
		<table id='navigationTable' width='100%' cellpadding='0' cellspacing='0'>
			<tr width='100%'>
				<td align='center' width='100%'>
					<div class='topBarTitle'>
						Songs
					</div>
				</td>

				<td align='right'>
					<div id='topBarNowPlaying'>
						Now Playing
					</div>
				</td>
			</tr>
		</table>

		<table id='playerText' width='100%' cellpadding='0' cellspacing='0' class='hidden'>
			<tr width='100%'>
				<td align='left'>
					<button id='playerExit'></button>
				</td>

				<td align='center' width='100%'>
					<div id='playerArtist'>Artist</div>
					<div id='playerTitle'>Title</div>
					<div id='playerAlbum'>Album</div>
				</td>
			</tr>
		</table>
	</div>

	<div id='playerSeekBarContainer' class='hidden'>
		<table width='100%' cellpadding='0' cellspacing='0'>
			<tr width='100%'>
				<td width='9%'>
					<div id='playerCurrentTime'>
						0:00
					</div>
				</td>

				<td width='82%'>
					<div id='playerSeekBarOuter'>
						<div id='playerSeekBarInner'>
							&nbsp;
						</div>
					</div>
				</td>

				<td width='9%' align='right'>
					<div id='playerTimeLeft'>
						0:00
					</div>
				</td>
			</tr>
		</table>
	</div>

	<div id='playerControls' class='hidden'>
		<table width='100%' cellpadding='0' cellspacing='0'>
			<tr width='100%'>
				<td width='33%' align='center'>
					<div id='playerBack'></div>
				</td>

				<td width='33%' align='center'>
					<div id='playerPause'></div>
					<div id='playerPlay'></div>
				</td>

				<td width='33%' align='center'>
					<div id='playerNext'></div>
				</td>
			</tr>
		</table>
	</div>
</div>

<div id='navigationBar'>
	<div data-page='playlist' class='navigationBarItem'> <br> Playlist</div>
	<div data-page='artists' class='navigationBarItem'> <br> Artists</div>
	<div id='navigationHome' data-page='songs' class='navigationBarItem navigationBarItemCurrent'> <br> Songs</div>
	<div data-page='albums' class='navigationBarItem'> <br> Albums</div>
	<div data-page='more' class='navigationBarItem'> <br> More</div>
</div>

<div id='navpage-playlist' class='hidden'>
	<div id='songPlaylist'>
	</div>
</div>

<div id='navpage-artists' class='hidden'>
	<?php

		$mQuery = $mysql->query("SELECT DISTINCT(artist) FROM `songs` ORDER BY `artist`");

		while($mData = $mQuery->fetch_assoc())
		{
			echo
			"<div class='menuItem artistAlbumContainer'>
				" . $mData['artist'] . "
			</div>";
		}

	?>
</div>

<div id='navpage-songs' class='hidden'>
	<div id='songLibrary'>
		<div class='searchContainer'>
			<input type='text' id='searchBar' placeholder=' Search' class='searchInput'>
		</div>

		<?php

			$mQuery = $mysql->query("SELECT * FROM `albums` ORDER BY RAND()");

			while($mData = $mQuery->fetch_assoc())
			{
				$songQuery = $mysql->query("SELECT * FROM `songs` WHERE `album` = '" . $mData['album'] . "'");

				while($songData = $songQuery->fetch_assoc())
				{
					$songsLoaded++;

					if($songData['album'] != $lastAlbum || !isset($lastAlbum))
					{
						echo "</span> <span class='albumGroup'>";
						$lastAlbum = $songData['album'];
					}

					songContainer($songData['path'], $songData['title'], $songData['artist'], $songData['album'], $songData['length']);
				}

				$mysql->query("UPDATE `record` SET `srecord` = CONCAT(srecord, '`id` <> " . $mData['id'] . " AND ') WHERE `unique_id` = '" . $_SESSION['unique_id'] . "'");

				if($songsLoaded >= SONGS_PER_LOAD)
				{
					break;
				}
			}

		?>
	</div>
</div>

<div id='navpage-albums' class='hidden'>
	<?php

		$mQuery = $mysql->query("SELECT DISTINCT(album) FROM `songs` ORDER BY `album`");

		while($mData = $mQuery->fetch_assoc())
		{
			echo
			"<div class='menuItem artistAlbumContainer'>
				" . $mData['album'] . "
			</div>";
		}

	?>
</div>

<div id='navpage-more' class='hidden'>
</div>

<script>
	$(document).ready(function()
	{
		var searchInterval = 0;

		$('#navpage-' + $('.navigationBarItemCurrent').data("page")).show();

		function mUnescape(text)
		{
			return text.replace("&#039;", "'").replace("&gt;", ">").replace("&quot;", "\"").replace("&lt;", "<");
		}

		function playNextSong()
		{
			$('#songPlaylist .songContainer').each(function()
			{
				$("#playerSong").jPlayer("setMedia",
				{
					mp3: mUnescape($(this).data("path")),
					wav: mUnescape($(this).data("path")),
					ogg: mUnescape($(this).data("path"))
				});

				$("#playerSong").jPlayer("play");

				$('#playerSongPath').text($(this).data("path"));

				$('#playerArtist').text($(this).data("artist"));
				$('#playerTitle').text($(this).data("title"));
				$('#playerAlbum').text($(this).data("album"));

				$(this).remove();
				return false;
			});
		}

		function displaySongContainers(display)
		{
			$('.songContainer').each(function()
			{
				if(display)
				{
					$(this).show();
				}
				else
				{
					$(this).hide();
				}
			});
		}

		function songContainerBinds()
		{
			$('#songLibrary .songContainer').unbind("click").click(function()
			{
				$(this).clone().appendTo('#songPlaylist');

				$('#songPlaylist .songContainer').click(function()
				{
					$(this).remove();
				});
			});
		}

		/* ===== Initialization ===== */

		songContainerBinds();

		$('.artistAlbumContainer').click(function()
		{
			var searchQuery = $(this).text().trim();

			$('#navigationHome').trigger("click");

			$('#searchBar').val(searchQuery);
			performSearch(searchQuery, 1);
		});

		/* ===== Top Bar ===== */

		$('#topBarNowPlaying').click(function()
		{
			$('#topBarContainer').removeClass("topBarContainerLight").addClass("topBarContainerDark").css("position", "static");
			$('#navigationTable, #songLibrary').hide();
			$('#playerText, #playerSeekBarContainer, #playerControls').show();
		});

		/* ===== Navigation Bar ===== */

		$('.navigationBarItem').click(function()
		{
			$('#navpage-' + $('.navigationBarItemCurrent').data("page")).hide();

			$('.navigationBarItemCurrent').removeClass("navigationBarItemCurrent");
			$(this).addClass("navigationBarItemCurrent");

			$('#navpage-' + $('.navigationBarItemCurrent').data("page")).show();
		});

		/* ===== Search ===== */

		function performSearch(query, likeSearch)
		{
			if(searchInterval != -1)
			{
				clearInterval(searchInterval);
			}

			if(query)
			{
				searchInterval = setInterval(function()
				{
					displaySongContainers(false);

					$('#searchResults').remove();

					$('#songLibrary').append("<div id='searchResults'><img src='images/loader.gif'></div>");

					$('#searchResults').load
					(
						(likeSearch) ? "esearch.php #songsSearched" : "search.php #songsSearched",
						{search: query},
						function()
						{
							songContainerBinds();
						}
					);

					clearInterval(searchInterval);
					searchInterval = -1;
				}, 250);
			}
			else
			{
				displaySongContainers(true);

				$('#searchResults').remove();
			}
		}

		$('#searchBar').keyup(function()
		{
			performSearch($(this).val(), 0);
		});

		/* ===== Player ===== */

		$("#playerSong").jPlayer(
		{
			swfPath: "js",
			solution: "html, flash",
			supplied: "mp3, wav, ogg",
			volume: 0.7,
			smoothPlayBar: true,
			cssSelectorAncestor: "#playerContainer",
			cssSelector:
			{
				play: "#playerPlay",
				pause: "#playerPause",
				currentTime: "#playerCurrentTime",
				duration: "#playerTimeLeft",
				seekBar: "#playerSeekBarOuter",
				playBar: "#playerSeekBarInner"
			},
			errorAlerts: false,
			warningAlerts: false,
			ended: function()
			{
				playNextSong();
			}
		});

		$('#playerNext').click(function()
		{
			playNextSong();
		});

		$('#playerExit').click(function()
		{
			$('#topBarContainer').removeClass("topBarContainerDark").addClass("topBarContainerLight").css("position", "absolute");
			$('#playerText, #playerSeekBarContainer, #playerControls').hide();
			$('#navigationTable, #songLibrary').show();
		});

		/* ===== ETC ===== */

		$(window).scroll(function()
		{
			//$('#topBarContainer').css("top", $(this).scrollTop());
			//$('#navigationBar').css("top", $(this).scrollTop() + $(window).height() - $('#navigationBar').height());

			if(!$('#searchBar').val())
			{
				if($(this).scrollTop() + $(this).height() > $(document).height() - 50)
				{
					$('body').append("<div id='scrollLoader'><img src='images/loader.gif'></div><div id='temporarySongLibrary'></div>");

					$('#temporarySongLibrary').load
					(
						"loadsongs.php #songsLoaded",
						function()
						{
							$('#scrollLoader').remove();

							$('#songLibrary').append($('#temporarySongLibrary').html());

							$('#temporarySongLibrary').remove();

							songContainerBinds();
						}
					);
				}
			}
		});
	});
</script>