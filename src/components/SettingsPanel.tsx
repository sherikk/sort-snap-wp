
import React, { useState } from "react";
import { Button } from "@/components/ui/button";
import { Switch } from "@/components/ui/switch";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Input } from "@/components/ui/input";
import { Checkbox } from "@/components/ui/checkbox";
import { toast } from "@/components/ui/sonner";
import { Save } from "lucide-react";

const SettingsPanel = () => {
  const [settings, setSettings] = useState({
    postTypes: ["post"],
    orderDirection: "asc",
    taxonomies: ["category"],
    enableFrontend: false,
    displayStyle: "list",
    adminRoles: ["administrator", "editor"],
  });

  const availablePostTypes = [
    { id: "post", name: "Posts" },
    { id: "page", name: "Pages" },
    { id: "product", name: "Products" },
    { id: "custom", name: "Custom Post Type" },
  ];

  const availableTaxonomies = [
    { id: "category", name: "Categories" },
    { id: "post_tag", name: "Tags" },
    { id: "custom", name: "Custom Taxonomy" },
  ];

  const userRoles = [
    { id: "administrator", name: "Administrator" },
    { id: "editor", name: "Editor" },
    { id: "author", name: "Author" },
    { id: "contributor", name: "Contributor" },
  ];

  const handlePostTypeToggle = (postType) => {
    setSettings((prev) => {
      if (prev.postTypes.includes(postType)) {
        return {
          ...prev,
          postTypes: prev.postTypes.filter((type) => type !== postType),
        };
      } else {
        return {
          ...prev,
          postTypes: [...prev.postTypes, postType],
        };
      }
    });
  };

  const handleRoleToggle = (role) => {
    setSettings((prev) => {
      if (prev.adminRoles.includes(role)) {
        return {
          ...prev,
          adminRoles: prev.adminRoles.filter((r) => r !== role),
        };
      } else {
        return {
          ...prev,
          adminRoles: [...prev.adminRoles, role],
        };
      }
    });
  };

  const saveSettings = () => {
    // This would be an API call in a real WordPress plugin
    console.log("Saving settings:", settings);
    toast.success("Settings saved successfully");
  };

  return (
    <div className="space-y-6">
      <div>
        <h3 className="text-lg font-medium">General Settings</h3>
        <p className="text-sm text-gray-500 mb-4">
          Configure how SortSnap works with your WordPress site
        </p>

        <div className="space-y-4">
          <div className="space-y-2">
            <Label>Post Types to Sort</Label>
            <div className="grid grid-cols-2 gap-2">
              {availablePostTypes.map((type) => (
                <div key={type.id} className="flex items-center space-x-2">
                  <Checkbox
                    id={`post-type-${type.id}`}
                    checked={settings.postTypes.includes(type.id)}
                    onCheckedChange={() => handlePostTypeToggle(type.id)}
                  />
                  <label
                    htmlFor={`post-type-${type.id}`}
                    className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  >
                    {type.name}
                  </label>
                </div>
              ))}
            </div>
          </div>

          <div className="flex items-center space-x-4">
            <div className="w-full md:w-1/2">
              <Label htmlFor="orderDirection">Default Order Direction</Label>
              <Select
                value={settings.orderDirection}
                onValueChange={(value) =>
                  setSettings({ ...settings, orderDirection: value })
                }
              >
                <SelectTrigger id="orderDirection">
                  <SelectValue placeholder="Select order" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="asc">Ascending</SelectItem>
                  <SelectItem value="desc">Descending</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div className="w-full md:w-1/2">
              <Label htmlFor="displayStyle">Display Style</Label>
              <Select
                value={settings.displayStyle}
                onValueChange={(value) =>
                  setSettings({ ...settings, displayStyle: value })
                }
              >
                <SelectTrigger id="displayStyle">
                  <SelectValue placeholder="Select style" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="list">List</SelectItem>
                  <SelectItem value="grid">Grid</SelectItem>
                  <SelectItem value="compact">Compact</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </div>
      </div>

      <Separator />

      <div>
        <h3 className="text-lg font-medium">Taxonomies</h3>
        <p className="text-sm text-gray-500 mb-4">
          Select which taxonomies to use for filtering posts
        </p>

        <div className="grid grid-cols-2 md:grid-cols-3 gap-2">
          {availableTaxonomies.map((tax) => (
            <div key={tax.id} className="flex items-center space-x-2">
              <Checkbox
                id={`tax-${tax.id}`}
                checked={settings.taxonomies.includes(tax.id)}
                onCheckedChange={() => {
                  setSettings((prev) => {
                    if (prev.taxonomies.includes(tax.id)) {
                      return {
                        ...prev,
                        taxonomies: prev.taxonomies.filter((t) => t !== tax.id),
                      };
                    } else {
                      return {
                        ...prev,
                        taxonomies: [...prev.taxonomies, tax.id],
                      };
                    }
                  });
                }}
              />
              <label
                htmlFor={`tax-${tax.id}`}
                className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
              >
                {tax.name}
              </label>
            </div>
          ))}
        </div>
      </div>

      <Separator />

      <div>
        <h3 className="text-lg font-medium">Permissions</h3>
        <p className="text-sm text-gray-500 mb-4">
          Control which user roles can use SortSnap
        </p>

        <div className="grid grid-cols-2 md:grid-cols-3 gap-2">
          {userRoles.map((role) => (
            <div key={role.id} className="flex items-center space-x-2">
              <Checkbox
                id={`role-${role.id}`}
                checked={settings.adminRoles.includes(role.id)}
                onCheckedChange={() => handleRoleToggle(role.id)}
              />
              <label
                htmlFor={`role-${role.id}`}
                className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
              >
                {role.name}
              </label>
            </div>
          ))}
        </div>
      </div>

      <Separator />

      <div>
        <h3 className="text-lg font-medium">Frontend Integration</h3>
        <p className="text-sm text-gray-500 mb-4">
          Configure how SortSnap affects your website frontend
        </p>

        <div className="flex items-center space-x-2 mb-4">
          <Switch
            id="frontend-enable"
            checked={settings.enableFrontend}
            onCheckedChange={(checked) =>
              setSettings({ ...settings, enableFrontend: checked })
            }
          />
          <Label htmlFor="frontend-enable">
            Apply custom sort order on frontend
          </Label>
        </div>

        {settings.enableFrontend && (
          <div className="pl-6 border-l-2 border-gray-200 space-y-4">
            <div>
              <Label htmlFor="shortcode">Shortcode</Label>
              <div className="flex mt-1">
                <Input
                  id="shortcode"
                  value="[sortsnap post_type='post' taxonomy='category' term='']"
                  readOnly
                  className="font-mono text-sm"
                />
                <Button
                  variant="ghost"
                  size="sm"
                  className="ml-2"
                  onClick={() => {
                    navigator.clipboard.writeText(
                      "[sortsnap post_type='post' taxonomy='category' term='']"
                    );
                    toast.success("Shortcode copied to clipboard");
                  }}
                >
                  Copy
                </Button>
              </div>
              <p className="text-xs text-gray-500 mt-1">
                Use this shortcode to display sorted posts on your pages
              </p>
            </div>
          </div>
        )}
      </div>

      <div className="flex justify-end pt-4">
        <Button onClick={saveSettings}>
          <Save className="mr-2 h-4 w-4" /> Save Settings
        </Button>
      </div>
    </div>
  );
};

export default SettingsPanel;
