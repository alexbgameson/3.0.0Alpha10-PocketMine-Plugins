![Alt text](https://salmonde.de/MCPE-Plugins/Pictures/StatsPE/StatsPE.png "StatsPE Icon")

# StatsPE - Advanced Stats Plugin [![Travis-CI](https://travis-ci.org/SalmonDE/StatsPE.svg?branch=master)](https://travis-ci.org/SalmonDE/StatsPE)

## Information

**Want to test the features before using them? Test them on this server: salmonde.de:19132**

**_Installation_** | **_StatsPE_**
------------------ | -------------------------------------------------------------------------------------------------------------------
Step 1             | Download the plugin [here](https://github.com/SalmonDE/StatsPE/releases/latest/) to get the latest pre-built phar!
Step 2             | After it has downloaded, drag the plugin into your **plugins** folder of your server files.
Step 3             | Start the server, and StatsPE has been added to your server!
_Optional_         | If you want to disable a statistic from showing on `/stats [player]`, replace `true` with `false` in the config.

--------------------------------------------------------------------------------

**_MySQL Configuration_** | **_Using MySQL with StatsPE_**
------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------
Step 4                    | Put your MySQL Settings in [these](https://github.com/SalmonDE/StatsPE/blob/master/resources/config.yml#L35-L38) lines.
Step 5                    | Change the data providers from JSON to MySQL in [this](https://github.com/SalmonDE/StatsPE/blob/master/resources/config.yml#L28) line.

--------------------------------------------------------------------------------

**_Command_**                       | **_Description_**                                                                          | **_Permission Node_**
----------------------------------- | ------------------------------------------------------------------------------------------ | ------------------------------
/stats                              | Shows the player's stats                                                                   | statspe.cmd.stats
/stats [player]                     | Shows the stats of another player                                                          | statspe.cmd.stats.others
/statspe                            | Shows statistics of the plugin                                                             | statspe.cmd.statspe
/statspe floatingtext add [name]    | Adds a floatingtext to your server at your current position                                | statspe.cmd.statspe.floatingtext
/statspe floatingtext remove [name] | Removes a floatingtext                                                                     | statspe.cmd.statspe.floatingtext
/statspe floatingtext list          | Lists all floatingtexts on the server                                                      | statspe.cmd.statspe.floatingtext
/statspe floatingtext info [name]   | Shows information about a floatingtext such as the position and shown stats                | statspe.cmd.statspe.floatingtext

--------------------------------------------------------------------------------

**FloatingText Setting** | **Description**                                                                                       | **Example**
------------------------ | ----------------------------------------------------------------------------------------------------- | ------------------------------------------------
Name                     | The name of the floating stat, specified by the creator                                               | SpawnText
Position                 | Contains information about the position of the floating stat (Array)                                  | X => '100' Y => '50' Z => '400' Level => 'Lobby'
Text                     | Contains text lines with statistics which will be shown on the floating text (Array)                  | OnlineTime: "Was online for {value}"

--------------------------------------------------------------------------------

**_Default Entries_**   | **_Description_**                             | **_Example_**                      | **_Does it save data?_**
-----------------       | --------------------------------------------- | ---------------------------------- | ----------
Username                | Name of the player (case sensitive)           | SalericioDE                        | Yes
Online                  | Is the player online                          | true (OR 1 in MySQL)               | Yes
ClientID                | ClientID of the MCPE installation             | -8655314918531                     | Yes
XBoxAuthenticated       | If the user is authenticated with Xbox or not | false (OR 0 in MySQL)              | Yes
LastIP                  | Last used IP from the player                  | 192.168.1.35                       | Yes
UUID                    | Player's UUID                                 | 3942e063-fa8f-3a43-8fc2-201dc6     | Yes
FirstJoin               | First time the player joined                  | 1491293910.542 (Unix timestamp)    | Yes
LastJoin                | Last time the player joined                   | 149129395750.641 (Unix timestamp)  | Yes
JoinCount               | How many times the player joined the server   | 10                                 | Yes
KillCount               | How often the player killed another player    | 69                                 | Yes
DeathCount              | How often the player died                     | 9                                  | Yes
K/D                     | Player's Kill/Death Ratio                     | 2,5                                | No
OnlineTime              | How long the player played on the server      | 321 (seconds)                      | Yes
BlockBreakCount         | How many blocks the player destroye           | 3                                  | Yes
BlockPlaceCount         | How many blocks the placer placed             | 4                                  | Yes
ChatCount               | How many chat messages the player sent        | 78                                 | Yes
ItemConsumeCount        | How often the player consumed an item         | 13                                 | Yes
ItemCraftCount          | How often the player crafted something        | 6                                  | Yes
ItemsDropCount          | How many items the player dropped             | 294                                | Yes
--------------------------------------------------------------------------------
