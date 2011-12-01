<?php
/**  
 *  Betaville Web Service - A service for accessing data from a Betaville server via HTTP requests
 *  Copyright (C) 2011 Skye Book <skye.book@gmail.com>
 *  
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
/**
* This class should be updated when the DBConst class in the main Java server
* is updated (and vice-versa)
*/
	define('DESIGN_TABLE', 'design');
	define('DESIGN_ID', 'designID');
	define('DESIGN_NAME', 'name');
	define('DESIGN_FILE', 'filepath');
	define('DESIGN_CITY', 'cityID');
	define('DESIGN_ADDRESS', 'address');
	define('DESIGN_USER', 'user');
	define('DESIGN_COORDINATE', 'coordinateID');
	define('DESIGN_DATE', 'date');
	define('DESIGN_LAST_MODIFIED', 'lastModified');
	define('DESIGN_PRIVACY', 'publicViewing');
	define('DESIGN_DESCRIPTION', 'description');
	define('DESIGN_URL', 'designURL');
	define('DESIGN_TYPE', 'designtype');
	define('DESIGN_TYPE_AUDIO', 'audio');
	define('DESIGN_TYPE_VIDEO', 'video');
	define('DESIGN_TYPE_MODEL', 'model');
	define('DESIGN_TYPE_SKETCH', 'sketch');
	define('DESIGN_TYPE_EMPTY', 'empty');
	define('DESIGN_IS_ALIVE', 'isAlive');
	define('DESIGN_FAVE_LIST', 'favelist');
	
	define('AUDIO_TABLE', 'audiodesign');
	define('AUDIO_ID', 'designid');
	define('AUDIO_LENGTH', 'length');
	define('AUDIO_VOLUME', 'volume');
	define('AUDIO_DIRECTIONX', 'directionX');
	define('AUDIO_DIRECTIONY', 'directionY');
	define('AUDIO_DIRECTIONZ', 'directionZ');
	
	define('VIDEO_TABLE', 'videodesign');
	define('VIDEO_ID', 'designid');
	define('VIDEO_LENGTH', 'length');
	define('VIDEO_VOLUME', 'volume');
	define('VIDEO_FORMAT', 'format');
	define('VIDEO_DIRECTIONX', 'directionX');
	define('VIDEO_DIRECTIONY', 'directionY');
	define('VIDEO_DIRECTIONZ', 'directionZ');
	
	define('SKETCH_TABLE', 'sketchdesign');
	define('SKETCH_ID', 'designid');
	define('SKETCH_ROTATION', 'rotY');
	define('SKETCH_LENGTH', 'length');
	define('SKETCH_WIDTH', 'width');
	define('SKETCH_UPPLANE', 'upPlane');
	
	define('MODEL_TABLE', 'modeldesign');
	define('MODEL_ID', 'designid');
	define('MODEL_ROTATION_X', 'rotX');
	define('MODEL_ROTATION_Y', 'rotY');
	define('MODEL_ROTATION_Z', 'rotZ');
	define('MODEL_LENGTH', 'length');
	define('MODEL_WIDTH', 'width');
	define('MODEL_HEIGHT', 'height');
	define('MODEL_TEX', 'textured');
	
	define('EMPTY_DESIGN_TABLE', 'emptydesign');
	define('EMPTY_DESIGN_ID', 'designid');
	define('EMPTY_DESIGN_LENGTH', 'length');
	define('EMPTY_DESIGN_WIDTH', 'width');
	
	define('CITY_TABLE', 'city');
	define('CITY_ID', 'cityID');
	define('CITY_NAME', 'cityName');
	define('CITY_STATE', 'state');
	define('CITY_COUNTRY', 'country');
	
	define('USER_TABLE', 'user');
	define('USER_NAME', 'userName');
	define('USER_DISPLAY_NAME', 'displayName');
	define('USER_PASS', 'userPass');
	define('USER_STRONG_PASS', 'strongpass');
	define('USER_STRONG_SALT', 'strongsalt');
	define('USER_TWITTER', 'twitterName');
	define('USER_EMAIL', 'email');
	define('USER_EMAIL_VISIBLE', 'showEmail');
	define('USER_ACTIVATED', 'activated');
	define('USER_CONFIRMCODE', 'confirmcode');
	define('USER_BIO', 'bio');
	define('USER_WEBSITE', 'website');
	define('USER_TYPE', 'type');
	define('USER_TYPE_MEMBER', 'member');
	define('USER_TYPE_BASE_COMMITTER', 'base_committer');
	define('USER_TYPE_DATA_SEARCHER', 'data_searcher');
	define('USER_TYPE_MODERATOR', 'moderator');
	define('USER_TYPE_ADMIN', 'admin');
	
	define('SESSION_TABLE', 'session');
	define('SESSION_ID', 'sessionID');
	define('SESSION_USER', 'userName');
	define('SESSION_START', 'timeEntered');
	define('SESSION_END', 'timeLeft');
	
	define('COORD_TABLE', 'coordinate');
	define('COORD_ID', 'coordinateID');
	define('COORD_NORTHING', 'northing');
	define('COORD_EASTING', 'easting');
	define('COORD_LATZONE', 'latZone');
	define('COORD_LONZONE', 'lonZone');
	define('COORD_ALTITUDE', 'altitude');
	
	define('COMMENT_TABLE', 'comment');
	define('COMMENT_ID', 'commentid');
	define('COMMENT_DESIGN', 'designid');
	define('COMMENT_USER', 'user');
	define('COMMENT_TEXT', 'comment');
	define('COMMENT_DATE', 'date');
	define('COMMENT_SPAMFLAG', 'spamFlag');
	define('COMMENT_SPAMVERIFIED', 'spamVerified');
	define('COMMENT_REPLIESTO', 'repliesTo');
	
	define('PROPOSAL_TABLE', 'proposal');
	define('PROPOSAL_ID', 'proposalID');
	define('PROPOSAL_SOURCE', 'sourceID');
	define('PROPOSAL_DEST', 'destinationID');
	define('PROPOSAL_TYPE', 'type');
	define('PROPOSAL_FEATURED', 'featured');
	define('PROPOSAL_TYPE_PROPOSAL', 'proposal');
	define('PROPOSAL_TYPE_VERSION', 'version');
	define('PROPOSAL_TYPE_REMOVABLE_LIST', 'removables');
	define('PROPOSAL_PERMISSIONS_LEVEL', 'level');
	define('PROPOSAL_PERMISSIONS_LEVEL_CLOSED', 'closed');
	define('PROPOSAL_PERMISSIONS_LEVEL_GROUP', 'group');
	define('PROPOSAL_PERMISSIONS_LEVEL_ALL', 'all');
	define('PROPOSAL_PERMISSIONS_GROUP_ARRAY', 'user_group');
	
	define('WORMHOLE_TABLE', 'wormhole');
	define('WORMHOLE_ID', 'wormholeid');
	define('WORMHOLE_COORDINATE', 'coordinateid');
	define('WORMHOLE_CITY', 'cityid');
	define('WORMHOLE_NAME', 'name');
	define('WORMHOLE_IS_ALIVE', 'isAlive');
?>