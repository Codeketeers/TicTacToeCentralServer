
delimiter $$

CREATE TRIGGER setGameIDs
BEFORE INSERT ON `t3`.`t3Match`
FOR EACH ROW
BEGIN
	DECLARE num1 float;
	
	SET NEW.GameID1 = CONCAT(NEW.MatchID, "1");
	SET NEW.GameID2 = CONCAT(NEW.MatchID, "2");
	SET NEW.GameID3 = CONCAT(NEW.MatchID, "3");
	
	INSERT INTO game SET GameID=NEW.GameID1, PlayerA = NEW.PlayerA, PlayerB = NEW.PlayerB, Turn = New.PlayerA;
	INSERT INTO game SET GameID=NEW.GameID2, PlayerA = NEW.PlayerA, PlayerB = NEW.PlayerB, Turn = New.PlayerB;
	
	SET @num1 = (SELECT RAND());
	
	IF @num1 >= .5 THEN
		INSERT INTO game SET GameID=NEW.GameID3, PlayerA = NEW.PlayerA, PlayerB = NEW.PlayerB, Turn = NEW.PlayerA;
	ELSE
		INSERT INTO game SET GameID=NEW.GameID3, PlayerA = NEW.PlayerA, PlayerB = NEW.PlayerB, Turn = NEW.PlayerB;
	END IF;
END$$

CREATE TRIGGER switchMove
BEFORE INSERT ON move
FOR EACH ROW
BEGIN
	DECLARE pA, pB,T CHAR(25);
	DECLARE cursor1 CURSOR FOR
	SELECT PlayerA, PlayerB, Turn FROM game WHERE GameID=NEW.GameID;
        
        OPEN cursor1;
        
        FETCH cursor1 INTO pA, pB, T;
        
        IF (T = pA) THEN
        	UPDATE game SET Turn=pB WHERE GameID=NEW.GameID;
        ELSE 
        	UPDATE game SET Turn=pA WHERE GameID=NEW.GameID;
        END IF;
        
        CLOSE cursor1;
END$$