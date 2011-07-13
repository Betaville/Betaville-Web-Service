<?php
/**
* This class should be updated when the DBConst class in the main Java server
* is updated (and vice-versa)
*/
	$DESIGN_TABLE = "design";
	$DESIGN_ID = "designID";
	$DESIGN_NAME = "name";
	$DESIGN_FILE = "filepath";
	$DESIGN_CITY = "cityID";
	$DESIGN_ADDRESS = "address";
	$DESIGN_USER = "user";
	$DESIGN_COORDINATE = "coordinateID";
	$DESIGN_DATE = "date";
	$DESIGN_LAST_MODIFIED = "lastModified";
	$DESIGN_PRIVACY = "publicViewing";
	$DESIGN_DESCRIPTION = "description";
	$DESIGN_URL = "designURL";
	$DESIGN_TYPE = "designType";
	$DESIGN_TYPE_AUDIO = "audio";
	$DESIGN_TYPE_VIDEO = "video";
	$DESIGN_TYPE_MODEL = "model";
	$DESIGN_TYPE_SKETCH = "sketch";
	$DESIGN_TYPE_EMPTY = "empty";
	$DESIGN_IS_ALIVE = "isAlive";
	$DESIGN_FAVE_LIST= "favelist";
	
	$AUDIO_TABLE = "audiodesign";
	$AUDIO_ID = "designid";
	$AUDIO_LENGTH = "length";
	$AUDIO_VOLUME = "volume";
	$AUDIO_DIRECTIONX = "directionX";
	$AUDIO_DIRECTIONY = "directionY";
	$AUDIO_DIRECTIONZ = "directionZ";
	
	$VIDEO_TABLE = "videodesign";
	$VIDEO_ID = "designid";
	$VIDEO_LENGTH = "length";
	$VIDEO_VOLUME = "volume";
	$VIDEO_FORMAT = "format";
	$VIDEO_DIRECTIONX = "directionX";
	$VIDEO_DIRECTIONY = "directionY";
	$VIDEO_DIRECTIONZ = "directionZ";
	
	$SKETCH_TABLE = "sketchdesign";
	$SKETCH_ID = "designid";
	$SKETCH_ROTATION = "rotY";
	$SKETCH_LENGTH = "length";
	$SKETCH_WIDTH = "width";
	$SKETCH_UPPLANE = "upPlane";
	
	$MODEL_TABLE = "modeldesign";
	$MODEL_ID = "designid";
	$MODEL_ROTATION_X = "rotX";
	$MODEL_ROTATION_Y = "rotY";
	$MODEL_ROTATION_Z = "rotZ";
	$MODEL_LENGTH = "length";
	$MODEL_WIDTH = "width";
	$MODEL_HEIGHT = "height";
	$MODEL_TEX = "textured";
	
	$EMPTY_DESIGN_TABLE = "emptydesign";
	$EMPTY_DESIGN_ID = "designid";
	$EMPTY_DESIGN_LENGTH = "length";
	$EMPTY_DESIGN_WIDTH = "width";
	
	$CITY_TABLE = "city";
	$CITY_ID = "cityID";
	$CITY_NAME = "cityName";
	$CITY_STATE = "state";
	$CITY_COUNTRY = "country";
	
	$USER_TABLE = "user";
	$USER_NAME = "userName";
	$USER_DISPLAY_NAME = "displayName";
	$USER_PASS = "userPass";
	$USER_STRONG_PASS = "strongpass";
	$USER_STRONG_SALT = "strongsalt";
	$USER_TWITTER = "twitterName";
	$USER_EMAIL = "email";
	$USER_EMAIL_VISIBLE = "showEmail";
	$USER_ACTIVATED = "activated";
	$USER_BIO = "bio";
	$USER_WEBSITE = "website";
	$USER_TYPE = "type";
	$USER_TYPE_MEMBER = "member";
	$USER_TYPE_BASE_COMMITTER = "base_committer";
	$USER_TYPE_DATA_SEARCHER = "data_searcher";
	$USER_TYPE_MODERATOR = "moderator";
	$USER_TYPE_ADMIN = "admin";
	
	$SESSION_TABLE = "session";
	$SESSION_ID = "sessionID";
	$SESSION_USER = "userName";
	$SESSION_START = "timeEntered";
	$SESSION_END = "timeLeft";
	
	$COORD_TABLE = "coordinate";
	$COORD_ID = "coordinateID";
	$COORD_NORTHING = "northing";
	$COORD_EASTING = "easting";
	$COORD_LATZONE = "latZone";
	$COORD_LONZONE = "lonZone";
	$COORD_ALTITUDE = "altitude";
	
	$COMMENT_TABLE = "comment";
	$COMMENT_ID = "commentID";
	$COMMENT_DESIGN = "designID";
	$COMMENT_USER = "user";
	$COMMENT_TEXT = "comment";
	$COMMENT_DATE = "date";
	$COMMENT_SPAMFLAG = "spamFlag";
	$COMMENT_SPAMVERIFIED = "spamVerified";
	$COMMENT_REPLIESTO = "repliesTo";
	
	$PROPOSAL_TABLE = "proposal";
	$PROPOSAL_ID = "proposalID";
	$PROPOSAL_SOURCE = "sourceID";
	$PROPOSAL_DEST = "destinationID";
	$PROPOSAL_TYPE = "type";
	$PROPOSAL_FEATURED = "featured";
	$PROPOSAL_TYPE_PROPOSAL = "proposal";
	$PROPOSAL_TYPE_VERSION = "version";
	$PROPOSAL_TYPE_REMOVABLE_LIST = "removables";
	$PROPOSAL_PERMISSIONS_LEVEL = "level";
	$PROPOSAL_PERMISSIONS_LEVEL_CLOSED = "closed";
	$PROPOSAL_PERMISSIONS_LEVEL_GROUP = "group";
	$PROPOSAL_PERMISSIONS_LEVEL_ALL = "all";
	$PROPOSAL_PERMISSIONS_GROUP_ARRAY = "group";
	
	$WORMHOLE_TABLE = "wormhole";
	$WORMHOLE_ID = "wormholeid";
	$WORMHOLE_COORDINATE = "coordinateid";
	$WORMHOLE_CITY = "cityid";
	$WORMHOLE_NAME = "name";
	$WORMHOLE_IS_ALIVE = "isAlive";
?>