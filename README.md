# auth.gg-wrapper
a api wrapper for auth.gg

This is a simple wrapper for auth.gg so you can use your own server for hosting.
To contact their server we are using their api.

# Use

First you got to make a networking object (i didn't  make get request found on uc)
```cpp
class networking
{
public:
	string replaceAll(string subject, const string& search,
		const string& replace) {
		size_t pos = 0;
		while ((pos = subject.find(search, pos)) != string::npos) {
			subject.replace(pos, search.length(), replace);
			pos += replace.length();
		}
		return subject;
	}

	string DownloadString(string URL) {
		HINTERNET interwebs = InternetOpenA("Mozilla/5.0", 1, NULL, NULL, NULL);
		HINTERNET urlFile;
		string rtn;
		if (interwebs) {
			urlFile = InternetOpenUrlA(interwebs, URL.c_str(), NULL, NULL, NULL, NULL);
			if (urlFile) {
				char buffer[2000];
				DWORD bytesRead;
				do {
					InternetReadFile(urlFile, buffer, 2000, &bytesRead);
					rtn.append(buffer, bytesRead);
					memset(buffer, 0, 2000);
				} while (bytesRead);
				InternetCloseHandle(interwebs);
				InternetCloseHandle(urlFile);
				string p = replaceAll(rtn, "|n", "\r\n");
				return p;
			}
		}
		InternetCloseHandle(interwebs);
		string p = replaceAll(rtn, "|n", "\r\n");
		return p;
	}

};
```

Then you import a md5wrapper for C++ kind of your own choice
Then here is a basic check
```cpp
	networking* server{ 0 };
	string response = server->DownloadString("http://mywebsite.com/api.php?check&token=" + license + "&hash=" + hwid);
	std::string success("200"+to_string(time(0));
	if (response.find(this->hash(success)) != string::npos) {
		 return true;
	}
```

# Get HWID
Are you having issues getting the HWID here is how i do it :)

```cpp
std::string exec(const char* cmd) {
	char buffer[128];
	std::string result = "";
	FILE* pipe = _popen(cmd, "r");
	if (!pipe) throw std::runtime_error("popen() failed!");
	try {
		while (fgets(buffer, sizeof buffer, pipe) != NULL) {
			result += buffer;
		}
	}
	catch (...) {
		_pclose(pipe);
		throw;
	}
	_pclose(pipe);
	return result;
}

vector<string> split(const string& str, const string& delim)
{
	vector<string> tokens;
	size_t prev = 0, pos = 0;
	do
	{
		pos = str.find(delim, prev);
		if (pos == string::npos) pos = str.length();
		string token = str.substr(prev, pos - prev);
		if (!token.empty()) tokens.push_back(token);
		prev = pos + delim.length();
	} while (pos < str.length() && prev < str.length());
	return tokens;
}

std::string get_hwid() {
	api* auth{ 0 };
	
	std::string output(exec("wmic diskdrive get serialnumber"));
	std::string newln = "\n";
	std::string compiled(split(output,newln)[1]);
	string hardware = md5(compiled);
	return hardware;
}
```

# IMPORTANT
Do keep in mind Auth.GG is insecure to MITM attacks so i would personally recommend Tenet Solutions
You can join their discord here [Tenet](https://discord.gg/AVz8umV)

# Credits
@Akex64 Making api wrapper
@Outbuilt Made auth.gg
