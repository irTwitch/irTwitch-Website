class Emote {
  constructor(id, image, zeroWidth) {
    this.id = id;
    this.image = image;
    this.zeroWidth = zeroWidth;
  }
}

class EmoteManager {
  constructor() {
    this.allEmotes = {};
    this.emojiMap =  {
        "4Head":"354","8-)":"555555579",":(":"2",":)":"1",":-(":"555555559",":-)":"555555557",":-\/":"555555586",":-D":"555555561",":-O":"555555581",":-P":"555555592",":-Z":"555555568",":-\\":"555555588",":-o":"555555583",":-p":"555555594",":-z":"555555566",":-|":"555555564",":D":"3",":O":"555555580",":P":"555555591",":Z":"555555567",":\\":"555555587",":o":"555555582",":p":"555555593",":z":"555555565",":|":"5",";)":"11",";-)":"555555590",";-P":"555555596",";-p":"555555598",";P":"13",";p":"555555597","&lt;3":"9","<3":"9","&gt;(":"4","ANELE":"3792","ArgieB8":"51838","ArsonNoSexy":"50","AsexualPride":"307827267","AsianGlow":"74","B)":"555555577","B-)":"555555578","BCWarrior":"30","BOP":"301428702","BabyRage":"22639","BatChest":"115234","BegWan":"160394","BibleThump":"86","BigBrother":"1904","BigPhish":"160395","BisexualPride":"307827313","BlackLivesMatter":"302537250","BlargNaut":"114738","BloodTrail":"69","BrainSlug":"115233","BrokeBack":"4057","BuddhaBar":"27602","Butterfinger":"emotesv2_ec588fb0f6a24faba4792f26027a6312","CaitlynS":"emotesv2_4acac638cffb4db49f376059f7077dae","CarlSmile":"166266","ChefFrank":"90129","CoolCat":"58127","CoolStoryBob":"123171","CorgiDerp":"49106","CrreamAwk":"191313","CurseLit":"116625","DAESuppy":"973","DBstyle":"73","DansGame":"33","DarkKnight":"emotesv2_d9567e500d78441793bee538dcabc1da","DarkMode":"461298","DatSheffy":"111700","DendiFace":"58135","DogFace":"114835","DoritosChip":"102242","DxCat":"110734","EarthDay":"959018","EleGiggle":"4339","EntropyWins":"376765","ExtraLife":"302426269","FBBlock":"1441276","FBCatch":"1441281","FBChallenge":"1441285","FBPass":"1441271","FBPenalty":"1441289","FBRun":"1441261","FBSpiral":"1441273","FBtouchdown":"626795","FUNgineer":"244","FailFish":"360","FallCry":"emotesv2_2734f1a85677416a9d8f846a2d1b4721","FallHalp":"emotesv2_7f9b025d534544afaf679e13fbd47b88","FallWinning":"emotesv2_dee4ecfb7f0940bead9765da02c57ca9","FamilyMan":"emotesv2_89f3f0761c7b4f708061e9e4be3b7d17","FootBall":"302628600","FootGoal":"302628617","FootYellow":"302628613","FrankerZ":"65","FreakinStinkin":"117701","FutureMan":"98562","GayPride":"307827321","GenderFluidPride":"307827326","GingerPower":"32","GivePLZ":"112291","GlitchCat":"304486301","GlitchLit":"304489128","GlitchNRG":"304489309","GrammarKing":"3632","GunRun":"1584743","HSCheers":"444572","HSWP":"446979","HarleyWink":"emotesv2_8b0ac3eee4274a75868e3d0686d7b6f7","HassaanChop":"20225","HeyGuys":"30259","HolidayCookie":"1713813","HolidayLog":"1713816","HolidayPresent":"1713819","HolidaySanta":"1713822","HolidayTree":"1713825","HotPokket":"357","HungryPaimon":"emotesv2_535e40afa0b34a9481997627b1b47d96","ImTyping":"emotesv2_b0c6ccb3b12b4f99a9cc83af365a09f1","IntersexPride":"307827332","InuyoFace":"160396","ItsBoshyTime":"133468","JKanStyle":"15","Jebaited":"114836","Jebasted":"emotesv2_031bf329c21040a897d55ef471da3dd3","JonCarnage":"26","KAPOW":"133537","KEKHeim":"emotesv2_7c5d25facc384c47963d25a5057a0b40","Kappa":"80393","KappaClaus":"74510","KappaPride":"55338","KappaRoss":"70433","KappaWealth":"81997","Kappu":"160397","Keepo":"1902","KevinTurtle":"40","Kippa":"1901","KomodoHype":"81273","KonCha":"160400","Kreygasm":"41","LUL":"425618","LaundryBasket":"emotesv2_ecb0bfd49b3c4325864b948d46c8152b","LesbianPride":"307827340","MVGame":"142140","Mau5":"30134","MaxLOL":"1290325","MechaRobot":"emotesv2_0be25a1663bd472495b91e0302cec166","MercyWing1":"1003187","MercyWing2":"1003189","MikeHogu":"81636","MingLee":"68856","ModLove":"emotesv2_a2dfbbbbf66f4a75b0f53db841523e6c","MorphinTime":"156787","MrDestructoid":"28","MyAvatar":"emotesv2_c0c9c932c82244ff920ad2134be90afb","NewRecord":"307763444","NinjaGrumpy":"138325","NomNom":"90075","NonbinaryPride":"307827356","NotATK":"34875","NotLikeThis":"58765","O.O":"555555572","O.o":"555555570","OSFrog":"81248","O_O":"555555571","O_o":"6","OhMyDog":"81103","OneHand":"66","OpieOP":"100590","OptimizePrime":"16","PJSalt":"36","PJSugar":"102556","PMSTwin":"92","PRChase":"28328","PanicVis":"3668","PansexualPride":"307827370","PartyHat":"965738","PartyTime":"135393","PeoplesChamp":"3412","PermaSmug":"27509","PicoMause":"111300","PinkMercy":"1003190","PipeHype":"4240","PixelBob":"1547903","PizzaTime":"emotesv2_f202746ed88f4e7c872b50b1f7fd78cc","PogBones":"emotesv2_30050f4353aa4322b25b6b044703e5d1","PogChamp":"305954156","Poooound":"117484","PopCorn":"724216","PoroSad":"emotesv2_4c39207000564711868f3196cc0a8748","PotFriend":"emotesv2_e02650251d204198923de93a0c62f5f5","PowerUpL":"425688","PowerUpR":"425671","PraiseIt":"38586","PrimeMe":"115075","PunOko":"160401","PunchTrees":"47","R)":"555555599","R-)":"555555600","RaccAttack":"114870","RalpherZ":"1900","RedCoat":"22","ResidentSleeper":"245","RitzMitz":"4338","RlyTho":"134256","RuleFive":"107030","RyuChamp":"emotesv2_0ebc590ba68447269831af61d8bc9e0d","SMOrc":"52","SSSsss":"46","SabaPing":"160402","SeemsGood":"64138","SeriousSloth":"81249","ShadyLulu":"52492","ShazBotstix":"87","Shush":"emotesv2_819621bcb8f44566a1bd8ea63d06c58f","SingsMic":"300116349","SingsNote":"300116350","SmoocherZ":"89945","SoBayed":"1906","SoonerLater":"2113050","Squid1":"191762","Squid2":"191763","Squid3":"191764","Squid4":"191767","StinkyCheese":"90076","StinkyGlitch":"304486324","StoneLightning":"17","StrawBeary":"114876","SuperVinlin":"118772","SwiftRage":"34","TBAngel":"143490","TF2John":"1899","TPFufun":"508650","TPcrunchyroll":"323914","TTours":"38436","TakeNRG":"112292","TearGlove":"160403","TehePelo":"160404","ThankEgg":"160392","TheIlluminati":"145315","TheRinger":"18","TheTarFu":"111351","TheThing":"7427","ThunBeast":"1898","TinyFace":"111119","TombRaid":"864205","TooSpicy":"114846","TransgenderPride":"307827377","TriHard":"120232","TwitchLit":"166263","TwitchRPG":"1220086","TwitchSings":"300116344","TwitchUnity":"196892","TwitchVotes":"479745","UWot":"134255","UnSane":"111792","UncleNox":"114856","VirtualHug":"301696583","VoHiYo":"81274","VoteNay":"106294","VoteYea":"106293","WTRuck":"114847","WholeWheat":"1896","WhySoSerious":"emotesv2_1fda4a1b40094c93af334f8b60868a7c","WutFace":"28087","YouDontSay":"134254","YouWHY":"4337","bleedPurple":"62835","cmonBruh":"84608","copyThis":"112288","duDudu":"62834","imGlitch":"112290","mcaT":"35063","o.O":"555555574","o.o":"555555576","o_O":"555555573","o_o":"555555575","panicBasket":"22998","pastaThat":"112289","riPepperonis":"62833","twitchRaid":"62836"
    };
  }

  async loadEmotes(channelID) {
    this.allEmotes = {};
    for (const [key, value] of Object.entries(this.emojiMap)) {
      this.allEmotes[key] = new Emote(value, `https://irtwitch.fun/chat_emoji/twitch/emojies/${value}.png`, false);
    }

    try {
      const response = await fetch(`https://api.frankerfacez.com/v1/set/3`);
      const data = await response.json();
      const emotes = data.set.emoticons;
      emotes.forEach((emote) => {
        if (emote.name && emote.id) {
          this.allEmotes[emote.name] = new Emote(
            emote.id.toString(),
            `https://irtwitch.fun/chat_emoji/betterttv_frankerfacez/emojies/${emote.id}.png`,
            false
          );
        }
      });
    } catch (e) {
      // Handle error
    }

    try {
      const response = await fetch(`https://7tv.io/v3/emote-sets/global`);
      const data = await response.json();
      const emotes = data.emotes;
      emotes.forEach((emote) => {
        if (emote.name && emote.id) {
          this.allEmotes[emote.name] = new Emote(
            emote.id.toString(),
            `https://irtwitch.fun/chat_emoji/7tv/emojies/${emote.id}.png`,
            false
          );
        }
      });
    } catch (e) {
      // Handle error
    }

    try {
      const response = await fetch(`https://api.frankerfacez.com/v1/room/id/${encodeURIComponent(channelID)}`);
      const data = await response.json();
      const setID = data.room.set;
      const emotes = data.sets[setID.toString()].emoticons;
      emotes.forEach((emote) => {
        if (emote.name && emote.id) {
          this.allEmotes[emote.name] = new Emote(
            emote.id.toString(),
            `https://irtwitch.fun/chat_emoji/betterttv_frankerfacez/emojies/${emote.id}.png`,
            false
          );
        }
      });
    } catch (e) {
      // Handle error
    }

    try {
      const response = await fetch(`https://7tv.io/v3/users/twitch/${encodeURIComponent(channelID)}`);
      const data = await response.json();
      const emotes = data.emote_set.emotes;
      emotes.forEach((emote) => {
        if (emote.name && emote.id) {
          this.allEmotes[emote.name] = new Emote(
            emote.id.toString(),
            `https://irtwitch.fun/chat_emoji/7tv/emojies/${emote.id}.png`,
            false
          );
        }
      });
    } catch (e) {
      // Handle error
    }

    try {
      const response = await fetch("https://api.betterttv.net/3/cached/emotes/global", {
        headers: {
          "content-type": "application/json",
        }
      });
      const data = await response.json();

      data.forEach((emote) => {
        if (emote.code && emote.id) {
          this.allEmotes[emote.code] = new Emote(
            emote.id.toString(),
            `https://irtwitch.fun/chat_emoji/betterttv/emojies/${emote.id}.png`,
            false
          );
        }
      });
    } catch (e) {
      // Handle error
    }

    // CROSS ORIGIN ERROR!
    // try {
    //   const response = await fetch("https://api.betterttv.net/3/cached/emotes/global", {
    //     headers: {
    //       "content-type": "application/json",
    //     }
    //   });
    //   const data = await response.json();

    //   data.forEach((emote) => {
    //     if (emote.code && emote.id) {
    //       this.allEmotes[emote.code] = new Emote(
    //         emote.id.toString(),
    //         `https://irtwitch.fun/chat_emoji/betterttv/emojies/${emote.id}.png`,
    //         false
    //       );
    //     }
    //   });
    // } catch (e) {
    //   // Handle error
    // }

    // try {
    //   const response = await fetch(`https://api.betterttv.net/3/cached/users/twitch/${encodeURIComponent(channelID)}`);
    //   const data = await response.json();
    //   const emotes = data.channelEmotes.concat(data.sharedEmotes);
    //   emotes.forEach((emote) => {
    //     if (emote.code && emote.id) {
    //       this.allEmotes[emote.code] = new Emote(
    //         emote.id.toString(),
    //         `https://irtwitch.fun/chat_emoji/betterttv/emojies/${emote.id}.png`,
    //         true
    //       );
    //     }
    //   });
    // } catch (e) {
    //   // Handle error
    // }
  }


  parseTwitchEmoji(message) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(message, 'text/html');
  
    const messageContentElements = doc.querySelectorAll('.message_content');
    messageContentElements.forEach(element => {
      const replacements = {};
  
      Object.entries(this.allEmotes).forEach(([key, emote]) => {
        if (element.textContent.includes(key)) {
          replacements[key] = `<img class="emote" src="${emote.image}" />`;
        }
      });
  
      const replacementKeys = Object.keys(replacements);
      replacementKeys.sort((a, b) => b.length - a.length);
  
      replacementKeys.forEach(replacementKey => {
        const regex = new RegExp(escapeRegExp(replacementKey), 'g');
        element.innerHTML = element.innerHTML.replace(regex, replacements[replacementKey]);
      });
    });
  
    return doc.documentElement.innerHTML;
  }
}
function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}