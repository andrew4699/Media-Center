<?php

	require_once("configuration/main.php");

	set_time_limit(1500);

	if(isMobile())
	{
		redirect("/mobile");
	}

	if($_POST['code'])
	{
		$_SESSION['code'] = $_POST['code'];
	}

	if($_SESSION['code'] != "ilikecake")
	{
		redirect("access");
	}

	if(isMobile())
	{
		echo "<div id='mobileWrapper' align='center'>";
	}
	else
	{
		echo "<div id='wrapper' align='center'>";
	}

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

<script src='js/jquery.jplayer.min.js'></script>
<script src='js/clamp.js'></script>

<div id='playerContainer'>
	<div id='playerSong'></div>
	<div id='playerSongEx'></div>

	<div id='playerSongPath' class='hidden'></div>

	<table cellpadding='0' cellspacing='0'>
		<tr>
			<td width='155' align='left'>
				<span id='playerBack'></span>
				<span id='playerPlay'></span>
				<span id='playerPause'></span>
				<span id='playerNext'></span>
			</td>

			<td id='playerSeekBarWrapper' align='center' class='noselect'>
				<div id='playerSeekBar'>
					<div id='playerSeekBarInner'>
						&nbsp;
					</div>
				</div>

				<span id='playerSeekBarInfo' class='noselect'>
					<table>
						<tr>
							<td width='280' align='center'>
								<span id='playerLike' data-liked='0' class='noselect'>
									<!---->
								</span>

								<span id='playerSongTitle'>
									No song loaded
								</span>
							</td>

							<td width='200' align='right'>
								<span id='playerCurrentTime'>
									5:21
								</span>

								/

								<span id='playerTotalTime'>
									9:99
								</span>
							</td>
						</tr>
					</table>
				</span>

				<script>
					$(document).ready(function()
					{
						$clamp(document.getElementById("playerSongTitle"), {clamp: 1});
					});
				</script>
			</td>

			<td width='10'></td>

			<td width='280' align='right'>
				<span id='playerTrash'></span>
				<span id='playerMix'></span>
				<span id='playerFavorite'></span>

				<span id='playerVolume'>
					<input type='range' id='playerVolumeSlider' min='0' max='100'>
				</span>
			</td>
		</tr>
	</table>
</div>

<table width='100%' height='100%' cellpadding='0' cellspacing='0'>
	<tr width='100%' height='100%'>
		<td id='mainNavigation' width='12%' height='10' valign='top' class='navigationContainer'>
			<div id='homePage' data-page='allsongs' class='navigationItem navigationItemCurrent'>All Songs</div>
			<div data-page='playlist' class='navigationItem'>Playlist - <span id='playlistCount'>0</span> <span id='notifications-playlist' class='hidden'></span></div>
			<div data-page='favorites' class='navigationItem'>Favorites</div>
			<div data-page='recentlyadded' class='navigationItem'>Recently Added</div>
			<div data-page='artists' class='navigationItem'>Artists</div>
			<div data-page='flagged' class='navigationItem'>Flagged</div>
			<div data-page='newgroup' class='navigationItem'>New Playlist</div>
			<div data-page='upload' class='navigationItem'>Upload</div>
			<a href='update' class='navigationItem'>Update</a>

			<?php

				$mQuery = $mysql->query("SELECT `name` FROM `groups`");

				while($mData = $mQuery->fetch_assoc())
				{
					echo "<div id='nav-gp-" . $mData['name'] . "' data-page='gp-" . $mData['name'] . "' data-group='" . $mData['name'] . "' class='navigationItem navigationGroup'>" . $mData['name'] . " <span id='n-gp-" . $mData['name'] . "' class='hidden'></span></div>";
				}

			?>
		</td>

		<td id='pageContent' valign='top' class='navigationContainer'>
			<div id='navpage-allsongs' class='hidden'>
				<table cellpadding='0' cellspacing='0' class='topBarContainer'>
					<tr>
						<td width='200'>
							<?php

								$mQuery = $mysql->query("SELECT `id` FROM `songs`");

								echo $mQuery->num_rows . " songs total";

							?>
						</td>

						<td width='700'>
							<button id='clearPlaylist'>Clear Playlist</button>
							<button id='addAllToPlaylist'>Add All to Playlist</button>
							<button id='tenRandom'>Ten Random Songs</button>
							<button id='shuffle'>Shuffle</button>
						</td>

						<td>
							<input type='text' id='search' placeholder=' Search' class='formInput'>
						</td>
					</tr>
				</table>

				<br>

				<div id='songLibrary'>

				<?php

					$nextColor = 0;

					$mQuery = $mysql->query("SELECT * FROM `albums` ORDER BY RAND()");

					while($mData = $mQuery->fetch_assoc())
					{
						$songQuery = $mysql->query("SELECT * FROM `songs` WHERE `album` = '" . $mData['album'] . "'");

						while($songData = $songQuery->fetch_assoc())
						{
							$songsLoaded++;

							if(!$songColor[($songData['album']) ? $songData['album'] : $songData['artist']])
							{
								$songColor[($songData['album']) ? $songData['album'] : $songData['artist']] = $tileColors[$nextColor];

								$nextColor++;

								if($nextColor == count($tileColors))
								{
									$nextColor = 0;
								}
							}

							if($songData['album'] != $lastAlbum || !isset($lastAlbum))
							{
								echo "</span> <span class='albumGroup'>";
								$lastAlbum = $songData['album'];
							}

							songContainer($songData['path'], $songData['title'], $songData['artist'], $songData['album'], $songData['length'], $songColor[($songData['album']) ? $songData['album'] : $songData['artist']], "songContainer", $songData['liked']);
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

			<div id='navpage-playlist' class='hidden'>
				<div id='songPlaylist'>
					<?php

						if($_COOKIE['mc_playlist'])
						{
							$mQuery = $mysql->query("SELECT `path`, `color`, `playing` FROM `playlist` WHERE `playlist` = '" . escape($_COOKIE['mc_playlist']) . "'");

							if($mQuery->num_rows)
							{
								while($mData = $mQuery->fetch_assoc())
								{
									$songQuery = $mysql->query("SELECT * FROM `songs` WHERE `path` = '" . $mData['path'] . "'");
									$songData = $songQuery->fetch_assoc();

									if($songData['album'] != $lastAlbum || !isset($lastAlbum))
									{
										echo "</span> <span class='albumGroup'>";
										$lastAlbum = $songData['album'];
									}

									songContainer($songData['path'], $songData['title'], $songData['artist'], $songData['album'], $songData['length'], $mData['color'], "playlistSongContainer");
								}
							}
							else
							{
								cookie("mc_playlist", md5(rand()));
							}
						}
						else
						{
							cookie("mc_playlist", md5(rand()));
						}

					?>
				</div>
			</div>

			<div id='navpage-favorites' class='hidden'>
				<?php

					$nextColor = 0;

					$songQuery = $mysql->query("SELECT * FROM `songs` WHERE `played` > '3' ORDER BY `played` DESC");

					while($songData = $songQuery->fetch_assoc())
					{
						if(!$songColor[($songData['album']) ? $songData['album'] : $songData['artist']])
						{
							$songColor[($songData['album']) ? $songData['album'] : $songData['artist']] = $tileColors[$nextColor];

							$nextColor++;

							if($nextColor == count($tileColors))
							{
								$nextColor = 0;
							}
						}

						if($songData['album'] != $lastAlbum || !isset($lastAlbum))
						{
							echo "</span> <span class='albumGroup'>";
							$lastAlbum = $songData['album'];
						}

						songContainer($songData['path'], $songData['title'], $songData['artist'], $songData['album'], $songData['length'], $songColor[($songData['album']) ? $songData['album'] : $songData['artist']], "songContainer", $songData['liked']);
					}

				?>
			</div>

			<div id='navpage-recentlyadded' class='hidden'>
				<?php

					$nextColor = 0;

					$songQuery = $mysql->query("SELECT * FROM `songs` ORDER BY `added` DESC");

					while($songData = $songQuery->fetch_assoc())
					{
						if(!$songColor[($songData['album']) ? $songData['album'] : $songData['artist']])
						{
							$songColor[($songData['album']) ? $songData['album'] : $songData['artist']] = $tileColors[$nextColor];

							$nextColor++;

							if($nextColor == count($tileColors))
							{
								$nextColor = 0;
							}
						}

						if($songData['album'] != $lastAlbum || !isset($lastAlbum))
						{
							echo "</span> <span class='albumGroup'>";
							$lastAlbum = $songData['album'];
						}

						songContainer($songData['path'], $songData['title'], $songData['artist'], $songData['album'], $songData['length'], $songColor[($songData['album']) ? $songData['album'] : $songData['artist']], "songContainer", $songData['liked']);
					}

				?>
			</div>

			<div id='navpage-artists' class='hidden'>
				<?php

					$nextColor = 0;

					$songQuery = $mysql->query("SELECT `artist` FROM `songs` WHERE `artist` <> '' ORDER BY `artist`");

					while($songData = $songQuery->fetch_assoc())
					{
						if(!$artistLoaded[$songData['artist']])
						{
							$artistLoaded[$songData['artist']] = true;

							$nextColor++;

							if($nextColor == count($tileColors))
							{
								$nextColor = 0;
							}

							songContainer("", $songData['artist'], "", "", "", $tileColors[$nextColor], "songArtistContainer");
						}
					}

				?>
			</div>

			<div id='navpage-flagged' class='hidden'>
				The songs listed here are flagged for having invalid meta data such as title, artist, and album.

				<br> <br>

				<?php

					$nextColor = 0;

					$songQuery = $mysql->query("SELECT * FROM `songs`");

					while($songData = $songQuery->fetch_assoc())
					{
						if(!$songData['path'] || !$songData['title'] || !$songData['artist'] || stripos($songData['album'], "http") !== false || stripos($songData['album'], "www") !== false || stripos($songData['album'], ".com") !== false || stripos($songData['album'], "mp3") !== false)
						{
							$nextColor++;

							if($nextColor == count($tileColors))
							{
								$nextColor = 0;
							}

							songContainer($songData['path'], $songData['title'], $songData['artist'], $songData['album'], $songData['length'], $tileColors[$nextColor], "songContainer", $songData['liked']);
						}
					}

				?>
			</div>

			<div id='navpage-newgroup' class='hidden'>
				<form id='newGroup' action='' method='POST' class='center'>
					<input type='text' id='groupname' placeholder='Playlist Name' class='formInput'> <input type='submit' value='Create Playlist' class='formBlueButton'>
				</form>
			</div>

			<div id='navpage-upload' class='hidden'>
				<?php

					if($_FILES['songs']['name'][0])
					{
						require_once("getid3/getid3.php");

						$getID3 = new getID3;

						foreach($_FILES['songs']['name'] as $fileIndex => $fileName)
						{
							$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

							if($fileExtension == "mp3" || $fileExtension == "wav" || $fileExtension == "ogg" || $fileExtension == "zip")
							{
								move_uploaded_file($_FILES['songs']['tmp_name'][$fileIndex], SONG_PATH . "/" . $_FILES['songs']['name'][$fileIndex]);

								if($fileExtension == "zip")
								{
									$zipFile = zip_open(SONG_PATH . "/" . $_FILES['songs']['name'][$fileIndex]);

									if($zipFile)
									{
										while($zipFileEntry = zip_read($zipFile))
										{
											$zipFileName = zip_entry_name($zipFileEntry);

											$zipFileExtension = pathinfo($zipFileName, PATHINFO_EXTENSION);

											if($zipFileExtension == "mp3" || $zipFileExtension == "wav" || $zipFileExtension == "ogg")
											{
												$zipFileHandle = zip_entry_open($zipFile, $zipFileEntry, "r");

												if($zipFileHandle)
												{
													file_put_contents(SONG_PATH . "/" . basename($zipFileName), zip_entry_read($zipFileEntry, zip_entry_filesize($zipFileEntry)));

													$songInfo = $getID3->analyze(SONG_PATH . "/" . basename($zipFileName));
													getid3_lib::CopyTagsToComments($songInfo);

													if(!$songInfo['comments_html']['title'][0])
													{
														$songInfo['comments_html']['title'][0] = basename($zipFileName);
													}

													$albumSongs[($songInfo['tags']['id3v2']['album'][0]) ? escape($songInfo['tags']['id3v2']['album'][0]) : escape($songInfo['comments_html']['artist'][0])] = true;
						
													$mysql->query("INSERT INTO `songs` (`path`, `title`, `artist`, `album`, `length`, `added`) VALUES ('" . SONG_PATH . "/" . escape(basename($zipFileName)) . "', '" . escape($songInfo['comments_html']['title'][0]) . "', '" . escape($songInfo['comments_html']['artist'][0]) . "', '" . escape($songInfo['tags']['id3v2']['album'][0]) . "', '" . escape($songInfo['playtime_string']) . "', '" . time() . "')");
												
													zip_entry_close($zipFileHandle);
												}
											}
										}

										zip_close($zipFile);
									}

									unlink(SONG_PATH . "/" . $_FILES['songs']['name'][$fileIndex]);
								}
								else
								{
									$songInfo = $getID3->analyze(SONG_PATH . "/" . basename($_FILES['songs']['name'][$fileIndex]));
									getid3_lib::CopyTagsToComments($songInfo);

									if(!$songInfo['comments_html']['title'][0])
									{
										$songInfo['comments_html']['title'][0] = basename($_FILES['songs']['name'][$fileIndex]);
									}

									$albumSongs[($songInfo['tags']['id3v2']['album'][0]) ? escape($songInfo['tags']['id3v2']['album'][0]) : escape($songInfo['comments_html']['artist'][0])] = true;
		
									$mysql->query("INSERT INTO `songs` (`path`, `title`, `artist`, `album`, `length`, `added`) VALUES ('" . SONG_PATH . "/" . escape(basename($_FILES['songs']['name'][$fileIndex])) . "', '" . escape($songInfo['comments_html']['title'][0]) . "', '" . escape($songInfo['comments_html']['artist'][0]) . "', '" . escape($songInfo['tags']['id3v2']['album'][0]) . "', '" . escape($songInfo['playtime_string']) . "', '" . time() . "')");
								}
							}
						}

						foreach($albumSongs as $arrayIndex => $arrayValue)
						{
							if($arrayIndex)
							{
								$mQuery = $mysql->query("SELECT `id` FROM `albums` WHERE `album` = '$arrayIndex'");

								if(!$mQuery->num_rows)
								{
									$mysql->query("INSERT INTO `albums` (`album`) VALUES ('$arrayIndex')");
								}
							}
						}

						redirect("index");
					}
					else if($_POST['fileURL'])
					{
						require_once("getid3/getid3.php");

						$getID3 = new getID3;

						$fileExtension = pathinfo(basename($_POST['fileURL']), PATHINFO_EXTENSION);

						if($fileExtension == "mp3" || $fileExtension == "wav" || $fileExtension == "ogg")
						{
							file_put_contents(SONG_PATH . "/" . basename($_POST['fileURL']), file_get_contents($_POST['fileURL']));

							$songInfo = $getID3->analyze(SONG_PATH . "/" . basename($_POST['fileURL']));

							getid3_lib::CopyTagsToComments($songInfo);

							unlink(SONG_PATH . "/" . basename($_POST['fileURL']));

							if(!$songInfo['comments_html']['title'][0])
							{
								$songInfo['comments_html']['title'][0] = basename($_POST['fileURL']);
							}

							$albumSongs[($songInfo['tags']['id3v2']['album'][0]) ? escape($songInfo['tags']['id3v2']['album'][0]) : escape($songInfo['comments_html']['artist'][0])] = true;
						
							$mysql->query("INSERT INTO `songs` (`path`, `title`, `artist`, `album`, `length`, `added`) VALUES ('" . escape($_POST['fileURL']) . "', '" . escape($songInfo['comments_html']['title'][0]) . "', '" . escape($songInfo['comments_html']['artist'][0]) . "', '" . escape($songInfo['tags']['id3v2']['album'][0]) . "', '" . escape($songInfo['playtime_string']) . "', '" . time() . "')");
						
							foreach($albumSongs as $arrayIndex => $arrayValue)
							{
								if($arrayIndex)
								{
									$mQuery = $mysql->query("SELECT `id` FROM `albums` WHERE `album` = '$arrayIndex'");

									if(!$mQuery->num_rows)
									{
										$mysql->query("INSERT INTO `albums` (`album`) VALUES ('$arrayIndex')");
									}
								}
							}

							redirect("index");
						}
						else
						{
							errorMessage("You may only upload MP3, WAV, and OGG files.");
						}
					}

				?>

				<form id='songUpload' action='' method='POST' enctype='multipart/form-data'>
					<table>
						<tr>
							<td width='100'>
								<input type='radio' id='upload_type_upload' name='upload_type' value='upload' checked> <label for='upload_type_upload'>Upload</label>
							</td>

							<td width='100'>
								<input type='radio' id='upload_type_url' name='upload_type' value='url'> <label for='upload_type_url'>URL</label>
							</td>
						</tr>
					</table>

					<br> <br> <br> <br>

					<div id='uploadMain' align='center'></div>
				</form>
			</div>

			<?php

				$mQuery = $mysql->query("SELECT * FROM `groups`");

				while($mData = $mQuery->fetch_assoc())
				{
					echo "<div id='navpage-gp-" . $mData['name'] . "' class='hidden'>";

					$groupSongQuery = $mysql->query("SELECT `path` FROM `groups_songs` WHERE `group` = '" . $mData['id'] . "'");

					while($groupSongData = $groupSongQuery->fetch_assoc())
					{
						$songQuery = $mysql->query("SELECT * FROM `songs` WHERE `path` = '" . $groupSongData['path'] . "'");
						$songData = $songQuery->fetch_assoc();

						if(!$songColor[($songData['album']) ? $songData['album'] : $songData['artist']])
						{
							$songColor[($songData['album']) ? $songData['album'] : $songData['artist']] = $tileColors[$nextColor];

							$nextColor++;

							if($nextColor == count($tileColors))
							{
								$nextColor = 0;
							}
						}

						if($songData['album'] != $lastAlbum || !isset($lastAlbum))
						{
							echo "</span> <span class='albumGroup'>";
							$lastAlbum = $songData['album'];
						}

						songContainer($songData['path'], $songData['title'], $songData['artist'], $songData['album'], $songData['length'], $songColor[($songData['album']) ? $songData['album'] : $songData['artist']], "songContainer", $songData['liked']);	
					}

					echo "</div>";
				}

			?>
		</td>
	</tr>
</table>

<script>
	$(document).ready(function()
	{
		var songPlaying = false, searchInterval = -1, contextSong = -1, playerMixing = false, previousSongs = new Array(), playlistCount = $('.playlistSongContainer').length;

		$('#playlistCount').text(playlistCount);

		jQuery.fn.reverse = [].reverse;

		$.fn.shuffle = function()
		{
	        var allElements = this.get(),
	            getRandom = function(maximum)
	            {
	                return Math.floor(Math.random() * maximum);
	            },
	            shuffled = $.map(allElements, function()
	            {
	                var random = getRandom(allElements.length), randomElement = $(allElements[random]).clone(true)[0];
	                allElements.splice(random, 1);
	                return randomElement;
	           });
	 
	        	this.each(function(elementIndex)
	        	{
	            	$(this).replaceWith($(shuffled[elementIndex]));
	        	});
	 
	        return $(shuffled);
	 
	    };

		function mUnescape(text)
		{
			return text.replace("&#039;", "'").replace("&gt;", ">").replace("&quot;", "\"").replace("&lt;", "<");
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

		function playNextSong(previous)
		{
			if(previous)
			{
				if(previousSongs.length)
				{
					var songData = previousSongs.pop(), songDataSplit = songData.split("|");

					$("#playerSong").jPlayer("setMedia",
					{
						mp3: mUnescape(songDataSplit[0]),
						wav: mUnescape(songDataSplit[0]),
						ogg: mUnescape(songDataSplit[0])
					});

					$("#playerSong").jPlayer("play");

					$('#playerSongPath').text(songDataSplit[0]);

					$('#playerFavorite').data("liked", ($(this).data("liked")) ? 0 : 1).css({"color": ($(this).data("liked")) ? "#43A6DF" : "white", "text-shadow": ($(this).data("liked")) ? "0px 0px 15px #43A6DF": "none"});

					$('#playerSongTitle').text(songDataSplit[1]);

					if(songDataSplit[2] == "Unknown")
					{
						$('#playerSongArtist').text("");
					}
					else
					{
						$('#playerSongArtist').text("by " + songDataSplit[2]);
					}

					if(songDataSplit[3])
					{
						$('#playerSongAlbum').text("on " + songDataSplit[3]);
					}
					else
					{
						$('#playerSongAlbum').text("");
					}

					$.post("played.php", {filepath: songDataSplit[0]});

					songPlaying = true;
				}
			}
			else
			{
				$('.playlistSongContainer').each(function()
				{
					$("#playerSong").jPlayer("setMedia",
					{
						mp3: mUnescape($(this).data("path")),
						wav: mUnescape($(this).data("path")),
						ogg: mUnescape($(this).data("path"))
					});

					$("#playerSong").jPlayer("play");

					$('#playerSongPath').text($(this).data("path"));

					$('#playerFavorite').data("liked", ($(this).data("liked")) ? 0 : 1).css({"color": ($(this).data("liked")) ? "#43A6DF" : "white", "text-shadow": ($(this).data("liked")) ? "0px 0px 15px #43A6DF": "none"});

					$('#playerSongTitle').text($(this).data("title"));

					if($(this).data("artist") == "Unknown")
					{
						$('#playerSongArtist').text("");
					}
					else
					{
						$('#playerSongArtist').text("by " + $(this).data("artist"));
					}

					if($(this).data("album"))
					{
						$('#playerSongAlbum').text("on " + $(this).data("album"));
					}
					else
					{
						$('#playerSongAlbum').text("");
					}

					$.post("played.php", {filepath: $(this).data("path")});

					playlistCount--;

					$('#playlistCount').text(playlistCount);

					songPlaying = true;
					$(this).remove();
					return false;
				});
			}
		}

		function addPlaylist(song, top)
		{
			playlistCount++;

			$('#playlistCount').text(playlistCount);

			if(top)
			{
				$(song).clone().prependTo("#songPlaylist").removeClass("songContainer").addClass("playlistSongContainer").find(".songInformation").remove();
			}
			else
			{
				$(song).clone().appendTo("#songPlaylist").removeClass("songContainer").addClass("playlistSongContainer").find(".songInformation").remove();
			}

			$('.playlistSongContainer').unbind("click").click(function()
			{
				$(this).remove();

				playlistCount--;

				$('#playlistCount').text(playlistCount);
			});

			$('#notifications-playlist').stop().hide().html("<span class='navigationNotification'>+1</span>").fadeIn(200, function()
			{
				$(this).fadeOut(2500);
			});

			if(!songPlaying && isSongPaused())
			{
				playNextSong(false);
			}
		}

		function numSelections()
		{
			return $('.ui-selected').length;
		}

		function songContainerBinds()
		{
			$('.songContainer').unbind("click").click(function()
			{
				addPlaylist(this, false);

				$(this).removeClass("ui-selected");
			});

			$('.songContainer').unbind("contextmenu").contextmenu(function(event)
			{
				$('#contextMenu').remove();

				contextSong = this;

				if($(this).hasClass("ui-selected") && numSelections() > 1)
				{
					var elementID = ($(this).parent().hasClass("albumGroup")) ? $(this).parent().parent().attr("id") : $(this).parent().attr("id");

					$('body').append("<div id='contextMenu'> \
						<div data-item='playSelectedNow'>Play Selected Now</div> \
						<div data-item='addSelectedTop'>Add Selected to Top</div> \
						<div data-item='addSelectedBottom'>Add Selected to Bottom</div> \
						<div data-item='deleteSelected'>Delete Selected</div> \
					</div>");

					if(elementID.indexOf("navpage-gp-") == -1)
					{
						$('#contextMenu').append("<div data-item='addSelectedGroup'>Add Selected to Playlist</div>");
					}
					else
					{
						$('#contextMenu').append("<div data-item='removeSelectedGroup'>Remove Selected From Playlist</div>");
					}

					$('#contextMenu div').click(function()
					{
						switch($(this).data("item"))
						{
							case "playSelectedNow":
							{
								$('.playlistSongContainer').each(function()
								{
									$(this).remove();

									playlistCount--;

									$('#playlistCount').text(playlistCount);
								});

								addPlaylist($('.ui-selected')[0], true);

								$('.ui-selected').reverse().each(function(index)
								{
									if(index != $('.ui-selected').length - 1)
									{
										addPlaylist(this, true);
									}
								});

								playNextSong();
								break;
							}

							case "addSelectedTop":
							{
								addPlaylist($('.ui-selected')[0], true);

								$('.ui-selected').reverse().each(function(index)
								{
									if(index != $('.ui-selected').length - 1)
									{
										addPlaylist(this, true);
									}
								});

								break;
							}

							case "addSelectedBottom":
							{
								$('.ui-selected').each(function()
								{
									addPlaylist(this, false);
								});

								break;
							}

							case "deleteSelected":
							{
								$('.ui-selected').each(function()
								{
									deleteSong($(this).data("path"));
								});

								break;
							}

							case "addSelectedGroup":
							{
								var groupNotice = "";

								$('.ui-selected').each(function()
								{
									$(this).data("tempselected", 1);
								});

								$('.navigationGroup').each(function()
								{
									groupNotice = groupNotice + "<option>" + $(this).data("group") + "</option>";
								});

								noticeBox("Please select the playlist you would like to put the song in \
									<br> <br> \
									\
									<div class='center'> \
										<select id='groupSelect' class='formSelect'> \
											<option selected disabled>Select a playlist</option> \
											" + groupNotice + " \
										</select> \
									</div>");

								$('#groupSelect').change(function()
								{
									$('.songContainer').each(function()
									{
										var currentElement = this;

										if($(this).data("tempselected"))
										{
											$.post("newgroup.php", {path: $(this).data("path"), groupname: $('#groupSelect').val()}, function()
											{
												$(currentElement).clone().appendTo("#navpage-gp-" + $('#groupSelect').val() + "");

												songContainerBinds();

												$('#n-gp-' + $('#groupSelect').val() + '').stop().hide().html("<span class='navigationNotification'>+1</span>").fadeIn(200, function()
												{
													$(this).fadeOut(2500);
												});
											});
										}
									});

									$('#popupCover').fadeOut(500, function()
									{
										$(this).remove();
									});
								});

								break;
							}

							case "removeSelectedGroup":
							{
								$('.ui-selected').each(function()
								{
									var currentElement = this, elementID = ($(this).parent().hasClass("albumGroup")) ? $(this).parent().parent().attr("id") : $(this).parent().attr("id");
									
									$.post("newgroup.php", {remove: $(this).data("path"), groupname: elementID.replace("navpage-gp-", "")}, function()
									{
										$(currentElement).remove();
									});
								});

								break;
							}
						}
					});
				}
				else
				{
					var elementID = ($(this).parent().hasClass("albumGroup")) ? $(this).parent().parent().attr("id") : $(this).parent().attr("id");

					$('body').append("<div id='contextMenu'> \
						<div data-item='playNow'>Play Now</div> \
						<div data-item='topOfPlaylist'>Top of Playlist</div> \
						<div data-item='bottomOfPlaylist'>Bottom of Playlist</div> \
						<div data-item='editSong'>Edit</div> \
						<div data-item='showSimilar'>Show Similar</div> \
						<div data-item='deleteSong'>Delete</div> \
					</div>");

					if($('#' + elementID).data("liked"))
					{
						$('#contextMenu').append("<div data-item='dislike'>Remove Star</div>");
					}
					else
					{
						$('#contextMenu').append("<div data-item='like'>Give Star</div>");
					}

					if(elementID.indexOf("navpage-gp-") == -1)
					{
						$('#contextMenu').append("<div data-item='addToGroup'>Add to Playlist</div>");
					}
					else
					{
						$('#contextMenu').append("<div data-item='removeFromGroup'>Remove From Playlist</div>");
					}

					$('#contextMenu div').click(function()
					{
						switch($(this).data("item"))
						{
							case "playNow":
							{
								addPlaylist(contextSong, true);
								playNextSong(false);
								break;
							}

							case "topOfPlaylist":
							{
								addPlaylist(contextSong, true);
								break;
							}

							case "bottomOfPlaylist":
							{
								addPlaylist(contextSong, false);
								break;
							}

							case "editSong":
							{
								confirmBox("greg is a scrub \
									<br> <br> \
									\
									<div class='center'> \
										<input type='text' id='editSong-Title' placeholder='Title' value='" + $(contextSong).data("title") + "' maxlength='150' class='formInput'> <br> \
										<input type='text' id='editSong-Artist' placeholder='Artist' value='" + $(contextSong).data("artist") + "' maxlength='150' class='formInput'> <br> \
										<input type='text' id='editSong-Album' placeholder='Album' value='" + $(contextSong).data("album") + "' maxlength='150' class='formInput'> <br> \
									</div>", "Save", "Cancel");

								$('#editSong-Title').focus();

								$('#popupButtonGood').click(function()
								{
									$.post("edit.php", {path: $(contextSong).data("path"), title: $('#editSong-Title').val(), artist: $('#editSong-Artist').val(), album: $('#editSong-Album').val()}, function()
									{
										$(contextSong).data("title", $('#editSong-Title').val()).data("artist", $('#editSong-Artist').val()).data("album", $('#editSong-Album').val());

										$(contextSong).find(".songTitle").text($('#editSong-Title').val());
										$(contextSong).find(".songArtist").text($('#editSong-Artist').val());
										$(contextSong).find(".songAlbum").text($('#editSong-Album').val());
									});
								});

								break;
							}

							case "showSimilar":
							{
								if(!$('#nav-similar').is(":visible"))
								{
									$('#mainNavigation').append("<div id='nav-similar' data-page='similar' class='navigationItem'>Similar Songs</div>");
									$('#pageContent').append("<div id='navpage-similar' class='hidden'></div>");
								}

								$('#navpage-similar').load
								(
									"similar.php #similarSongs",
									{artist: $(contextSong).data("artist")},
									function()
									{
										bindNavigation();

										songContainerBinds();

										$('#nav-similar').trigger("click");
									}
								);
							
								break;
							}

							case "deleteSong":
							{
								deleteSong($(contextSong).data("path"));
								break;
							}

							case "addToGroup":
							{
								var groupNotice = "";

								$('.navigationGroup').each(function()
								{
									groupNotice = groupNotice + "<option>" + $(this).data("group") + "</option>";
								});

								noticeBox("Please select the playlist you would like to put the song in \
									<br> <br> \
									\
									<div class='center'> \
										<select id='groupSelect' class='formSelect'> \
											<option selected disabled>Select a playlist</option> \
											" + groupNotice + " \
										</select> \
									</div>");

								$('#groupSelect').change(function()
								{
									$.post("newgroup.php", {path: $(contextSong).data("path"), groupname: $('#groupSelect').val()}, function()
									{
										$(contextSong).clone().appendTo("#navpage-gp-" + $('#groupSelect').val() + "");

										songContainerBinds();

										$('#n-gp-' + $('#groupSelect').val() + '').stop().hide().html("<span class='navigationNotification'>+1</span>").fadeIn(200, function()
										{
											$(this).fadeOut(2500);
										});
									});

									$('#popupCover').fadeOut(500, function()
									{
										$(this).remove();
									});
								});

								break;
							}

							case "removeFromGroup":
							{
								var elementID = ($(contextSong).parent().hasClass("albumGroup")) ? $(contextSong).parent().parent().attr("id") : $(contextSong).parent().attr("id");
								
								$.post("newgroup.php", {remove: $(contextSong).data("path"), groupname: elementID.replace("navpage-gp-", "")}, function()
								{
									$(contextSong).remove();
								});

								break;
							}

							case "like":
							{
								likeSong(contextSong, true);
								break;
							}

							case "dislike":
							{
								likeSong(contextSong, false);
								break;
							}
						}
					});
				}

				$('#contextMenu').css({top: event.pageY, left: event.pageX});
				return false;
			});

			$('#songLibrary, #navpage-favorites').selectable(
		    {
		    	filter: ".songContainer"
		    });

		    $('.navigationGroup').each(function()
		    {
		    	$('#navpage-gp-' + $(this).data("group") + '').selectable(
		    	{
		    		filter: ".songContainer"
		    	});
		    });
		}

		function confirmBox(text, good, bad)
		{
			if(typeof(good) == "undefined")
			{
				good = "Yes";
			}

			if(typeof(bad) == "undefined")
			{
				bad = "No";
			}

			$('#popupButtonGood, #popupButtonBad').unbind("click");

			$('body').append("<div id='popupCover' align='center' class='hidden'> \
				<div class='popupContainer'> \
					" + text + " \
					\
					<br> <br> \
					\
					<div class='center'> \
						<button id='popupButtonGood'>" + good + "</button> <button id='popupButtonBad'>" + bad + "</button> \
					</div> \
				</div> \
			</div>");

			$('#popupCover').fadeIn(500);

			$('#popupButtonGood, #popupButtonBad').click(function()
			{
				$('#popupCover').fadeOut(500, function()
				{
					$(this).remove();
				});
			});
		}

		function noticeBox(text)
		{
			$('body').append("<div id='popupCover' align='center' class='hidden'> \
				<div class='popupContainer'> \
					" + text + " \
					\
					<br> <br> \
					\
					<div class='center'> \
						<button id='popupButtonBad'>Close</button> \
					</div> \
				</div> \
			</div>");

			$('#popupCover').fadeIn(500);

			$('#popupButtonBad').click(function()
			{
				$('#popupCover').fadeOut(500);
			});
		}

		function quickPopup(text)
		{
			$('#navpage-playlist').prepend("<div id='formSuccessNotice' class='formSuccessNotice'> \
				" + text + " \
			</div> <br>");

			$('#formSuccessNotice').fadeOut(5000);
		}

		function deleteSong(path)
		{
			$.post("delete.php", {filepath: path}, function()
			{
				$('.songContainer').each(function()
				{
					if($(this).data("path") == path)
					{
						$(this).remove();
					}
				});
			});

			if($('#playerSongPath').text() == path)
			{
				$("#playerSong").jPlayer("stop");
			}
		}

		function songLoaded()
		{
			return $('#playerSongTitle').text().indexOf("No song loaded") == -1;
		}

		function likeSong(song, like)
		{
			$(song).data("liked", like);
			$(this).css({"color": (!like) ? "#43A6DF" : "white", "text-shadow": (!like) ? "0px 0px 15px #43A6DF": "none"});

			$.post("liked.php", {liked: like, path: $(song).data("path")});

			if(like)
			{
				$(song).find(".songTitle").text(" " + $(song).data("title"));
				$(song).data("title", " " + $(song).data("title"));
			}
			else
			{
				$(song).find(".songTitle").text($(song).data("title").substring(2));
				$(song).data("title", $(song).data("title").substring(2));
			}

			if($('#playerSongPath') == $(song).data("path"))
			{
				$('#playerSongTitle').text($(song).data("title"));
			}

			return false;
		}

		/* ========== Initialization ========= */

		songContainerBinds();

		$('#songPlaylist').sortable(
		{
			items: ".playlistSongContainer"
		});

		$('.playlistSongContainer').unbind("click").click(function()
		{
			$(this).remove();

			playlistCount--;

			$('#playlistCount').text(playlistCount);
		});

		$('.songArtistContainer').unbind("click").click(function()
		{
			$('#homePage').trigger("click");

			$('#search').val($(this).data("title"));
			performSearch($(this).data("title"));
		});

		$('#playerVolumeBarSlider').slider();

		/* ========== Search ========== */

		function performSearch(query)
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
						"search.php #songsSearched",
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

		$('#search').keyup(function()
		{
			performSearch($(this).val());
		});

		/* ========== Player ========== */

		function isSongPaused()
	    {
	    	return $("#playerSong").data().jPlayer.status.paused;
	    }

	    function getCurrentTime()
	    {
	    	return $("#playerSong").data("jPlayer").status.currentTime;
	    }

	    function getTotalTime()
	    {
	    	return $("#playerSong").data("jPlayer").status.duration;
	    }

	    function getPlayerVolume()
	    {
	    	return $('#playerSong').data("jPlayer").options.volume;
	    }

	    function initializePlayer()
	    {
	    	$("#playerSong").jPlayer(
			{
				swfPath: "js",
				solution: "flash, html",
				supplied: "mp3, wav, ogg",
				volume: 0.7,
				smoothPlayBar: true,
				keyEnabled: true,
				keyBindings:
				{
					play:
					{
						key: 32,
						fn: function(player)
						{
							if(player.status.paused)
							{
								player.play();
							}
							else
							{
								player.pause();
							}
						}
					}
				},
				cssSelectorAncestor: "#playerContainer",
				cssSelector:
				{
					play: "#playerPlay",
					pause: "#playerPause",
					mute: "#playerVolumeMute",
					unmute: "#playerVolumeUnmute",
					volumeBar: "#playerVolumeOuter",
					volumeBarValue: "#playerVolumeInner",
					volumeMax: "#playerVolumeMax",
					currentTime: "#playerCurrentTime",
					duration: "#playerTotalTime",
					seekBar: "#playerSeekBar",
					playBar: "#playerSeekBarInner"
				},
				errorAlerts: false,
				warningAlerts: false,
				timeupdate: function()
				{
					if(playerMixing)
					{
						if(getTotalTime() - getCurrentTime() <= 10)
						{
							
						}
					}
				},
				ended: function()
				{
					previousSongs.push($('#playerSongPath').text() + "|" + $('#playerSongTitle').text() + "|" + $('#playerSongArtist').text() + "|" + $('#playerSongAlbum').text());

					songPlaying = false;

					playNextSong(false);
				}
			});
	    }

		initializePlayer();

		$('#playerFavorite').click(function()
		{
			$(this).data("liked", ($(this).data("liked")) ? 0 : 1);
			$(this).css({"color": (!$(this).data("liked")) ? "#43A6DF" : "white", "text-shadow": (!$(this).data("liked")) ? "0px 0px 15px #43A6DF": "none"});
		
			$.post("liked.php", {liked: !$(this).data("liked"), path: $('#playerSongPath').text()});

			if(!$(this).data("liked"))
			{
				$('.songContainer').each(function()
				{
					if($(this).data("path") == $('#playerSongPath').text())
					{
						$(this).find(".songTitle").text(" " + $(this).data("title"));
						$(this).data("title", " " + $(this).data("title"));
						$('#playerSongTitle').text($(this).data("title"));
						return false;
					}
				});
			}
			else
			{
				$('.songContainer').each(function()
				{
					if($(this).data("path") == $('#playerSongPath').text())
					{
						$(this).find(".songTitle").text($(this).data("title").substring(2));
						$(this).data("title", $(this).data("title").substring(2));
						$('#playerSongTitle').text($(this).data("title"));
						return false;
					}
				});
			}

			likeSong(!$(this).data("liked"));
		});

		$('#playerVolumeSlider').val(70).change(function()
		{
			$("#playerSong").jPlayer("volume", $(this).val() / 100);
		});

		$('#playerBack').click(function()
		{
			songPlaying = false;

			playNextSong(true);
		});

		$('#playerNext').click(function()
		{
			previousSongs.push($('#playerSongPath').text() + "|" + $('#playerSongTitle').text() + "|" + $('#playerSongArtist').text() + "|" + $('#playerSongAlbum').text());

			songPlaying = false;

			playNextSong(false);
		});

		$('#playerTrash').click(function()
		{
			if(songLoaded())
			{
				confirmBox("Are you sure you want to delete '" + $('#playerSongTitle').text() + "'?");

				$('#popupButtonGood').click(function()
				{
					deleteSong($('#playerSongPath').text());
					playNextSong(false);
				});
			}
		});

		$('#playerMix').click(function()
		{
			playerMixing = !playerMixing;
			$(this).css({"color": (playerMixing) ? "#43A6DF" : "white", "text-shadow": (playerMixing) ? "0px 0px 15px #43A6DF": "none"});
		});

		/* ========== Navigation ========== */

		function bindNavigation()
		{
			$('.navigationItem').click(function()
			{
				$('#navpage-' + $('.navigationItemCurrent').data("page") + '').hide();

				$('.navigationItemCurrent').removeClass("navigationItemCurrent");

				$(this).addClass("navigationItemCurrent");

				$('#navpage-' + $(this).data("page") + '').show();

				if($(this).data("page") == "playlist")
				{
					quickPopup("You may drag the songs in the playlist to rearrange them.");
				}
			});
		}

		bindNavigation();

		$('.navigationItemCurrent').trigger("click");

		/* ========== Groups ========== */

		$('#newGroup').submit(function()
		{
			$.post("newgroup.php", {name: $('#groupname').val()}, function()
			{
				$('#mainNavigation').append("<div id='nav-gp-" + $('#groupname').val() + "' data-page='gp-" + $('#groupname').val() + "' data-group='" + $('#groupname').val() + "' class='navigationItem navigationGroup'>" + $('#groupname').val() + " <span id='n-gp-" + $('#groupname').val() + "' class='hidden'></span></div>");
				$('#pageContent').append("<div id='navpage-gp-" + $('#groupname').val() + "' class='hidden'></div>");
			
				bindNavigation();

				$('#groupname').val("");
			});

			return false;
		});

		/* ========== Upload ========== */

		$('input[name="upload_type"]').change(function()
		{
			switch($('input[name="upload_type"]:checked').val())
			{
				case "upload":
				{
					$('#uploadMain').html("<input type='hidden' name='MAX_FILE_SIZE' value='2147483646'><input type='file' id='fileUpload' name='songs[]' multiple>");
					break;
				}

				case "url":
				{
					$('#uploadMain').html("<input type='text' name='fileURL' placeholder='URL' size='35' maxlength='150' class='formInput'> <input type='submit' value='Upload' class='formBlueButton'>");
					break;
				}
			}
		});

		$('#upload_type_upload').trigger("change");

		$('#fileUpload').change(function()
		{
			$('#songUpload').submit();
		});

		$('#songUpload').submit(function()
		{
			$(this).hide();

			$('#pageContent').append("<div id='pageContentLoader'><img src='images/loader.gif'></div>");
		});

		/* ========== Top Bar ========== */

		$('#clearPlaylist').click(function()
		{
			$('.playlistSongContainer').each(function()
			{
				$(this).remove();
			});

			playlistCount = 0;

			$('#playlistCount').text(playlistCount);
		});

		$('#addAllToPlaylist').click(function()
		{
			$('#songLibrary .songContainer').each(function()
			{
				addPlaylist(this, false);
			});
		});

		$('#tenRandom').click(function()
		{
			var randomSongs = jQuery('.songContainer').get().sort(function()
			{ 
			  return Math.round(Math.random()) - 0.5
			}).slice(0, 10)

			addPlaylist(randomSongs, false);

			playlistCount += 10;

			$('#playlistCount').text(playlistCount);
		});

		$('#shuffle').click(function()
		{
			$('.songContainer').shuffle();
		});

		/* ========== ETC ========== */

		$(document).click(function(event)
		{
			if($(event.target).parents().index(".songContainer") == -1 && $(event.target).parents().index("#contextMenu") == -1)
			{
				$('.ui-selected').each(function()
				{
					$(this).removeClass("ui-selected");
				});
			}

			$('#contextMenu').remove();
		});

		$(window).scroll(function()
		{
			$('#search').css({"position": "relative", "top": $(this).scrollTop()});

			//$('#mainNavigation').css({"position": "relative", "top": $(this).scrollTop()});

			if($(this).scrollTop() + $(this).height() == $(document).height())
			{
				if(!$('#scrollLoader').is(":visible") && !$('#searchResults').is(":visible"))
				{
					$('#pageContent').append("<div id='scrollLoader'><img src='images/loader.gif'></div><div id='temporarySongLibrary'></div>");

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

		setInterval(function()
		{
			$.post("playlist.php", {clear: true});

			$('.playlistSongContainer').each(function()
			{
				$.post("playlist.php", {path: $(this).data("path"), color: $(this).css("background")});
			});

			$.post("volume.php", {volume: getPlayerVolume()});
		}, 60000);
	});
</script>