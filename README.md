# Table of Contents #
1. [Pairing/Handshaking](#1)
2. [Game Play](#2)
3. [Game Log](#3)

## General Info ##
All pages can be sent a key value pair of "Debug" = "true" to view more detailed output 

This output, however, is not currently in JSON form, so it may cause errors unless handled correctly.

Any variable inside brackets [ ] is optional for both sending and receiving

## <a name="#1"/> 1. Pairing/Handshaking - ServerPairing.php ##
	Input - 
		challenge = text
			- Name of who you want to challenge.
		from = text
			- Your name.


	Output -
		gameID = #
			- Id of the game, needed to be sent for each move.
		message = text
			- A human readable message about the challenge.
		challenger = text
			- Name of the opponent.
		[yourTurn] = true
			- You will only receive this if you play first.
		[x] = #
			- X loc of first play if you play second.
		[y] = #
			- Y loc of first play if you play second.
		[error] = text
			- All encompassing error, shows for incorrect values sent or server timeout.

## <a name="#2"/> Game Play - Play.php ##
Regular tic-tac-toe play function

	Input -
		x = #
			- X location to play at.
		y = #
			- Y location to play at.
		from = text
			- Your name.
		gameID = #
			- The game id given from pairing.
		[flags] = enum('winning move', 'draw move', 'challenge win', 'challenge move', 'accept loss')
			- Sends extra information such as
				'winning move' - When you play the winning move.
				'draw move' - When you play the 8 move of a game where there is no winner.
				'challenge win' - To challenge the opponents 'winning move', if you do not send 'challenge win', then you are admitting defeat.
				'challenge move' - To challenge the most recent opponents move, if you do not send 'challenge move', then the move is considered valid.
				'accept loss' - After you receive and validate a 'winning move'.

		* If either 'challenge move' or 'challenge win' are sent then the game is invalidated and the match is exited, x and y are also ignored.

		Output - 
			x = #
				- X location opponent chooses.
			y = #
				- Y location opponent chooses.
 			time = timestamp
				- Time stamp of when the move was submitted.
			[flags] = enum('winning move', 'draw move', 'challenge win', 'challenge move')
				- The flags, if any, sent to the server from the opponent.
			[error] = text
				- All encompassing error, shows for incorrect values sent or server timeout.

## <a name="#3"/> Game Log - RequestLog.php ##
Request a log of the game

	Input -
		from = text
			- Your name.
		gameID = #
			- The game id given from pairing.
		
	Output - 
		log = text
			- A log of the game.
		[error] = text
			- All encompassing error, shows for incorrect values sent or server timeout.